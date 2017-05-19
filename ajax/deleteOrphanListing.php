<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('TMP_DS')) {
    define('TMP_DS', DIRECTORY_SEPARATOR);
}

require_once dirname(__FILE__).TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'config'.TMP_DS.'config.inc.php';
include_once dirname(__FILE__).TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'init.php';
include '../ebay.php';

$ebay = new Ebay();

$ebay_request = new EbayRequest();

$id_product_ref = Tools::getValue('id_product_ref');

if (!Configuration::get('EBAY_SECURITY_TOKEN') || Tools::getValue('token') != Configuration::get('EBAY_SECURITY_TOKEN')) {
    return Tools::safeOutput(Tools::getValue('not_logged_str'));
}
$product = EbayProduct::getProductsIdFromItemId($id_product_ref);
if (Tools::getValue('action') == "end") {
    EbayTaskManager::deleteTaskForPorductAndEbayProfile($product['id_product'], $product['id_ebay_profile']);
    $product_ps = new Product($product['id_product'], false, Tools::getValue('id_lang'));
    EbayTaskManager::addTask('end', $product_ps, Tools::getValue('id_employee'), $product['id_ebay_profile'], $product['id_product_attribute']);
}

if (Tools::getValue('action') == "out_of_stock") {
    EbayProductConfiguration::insertOrUpdate($product['id_product'], array(
        'id_ebay_profile' => $product['id_ebay_profile'],
        'blacklisted' =>  1,
        'extra_images' => 0,
    ));
    EbayTaskManager::deleteTaskForPorduct($product['id_product']);
    $ebay_profile = new EbayProfile($product['id_ebay_profile']);
    if ($product) {
        $productPS = new Product($product['id_product']);
        EbayTaskManager::addTask('mod', $productPS, Tools::getValue('id_employee'), Tools::getValue('id_ebay_profile'), $product['id_product_attribute']);
        EbayTaskManager::deleteTaskForOutOfStock($product['id_product'], $product['id_ebay_profile']);
    }
}
echo '1';
