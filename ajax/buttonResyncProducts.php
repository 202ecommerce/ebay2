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

require_once dirname(__FILE__).'/../../../config/config.inc.php';
require_once dirname(__FILE__).'/../../../init.php';
require_once '../ebay.php';

if (!Configuration::get('EBAY_SECURITY_TOKEN')
    || Tools::getValue('token') != Configuration::get('EBAY_SECURITY_TOKEN')) {
    return Tools::safeOutput(Tools::getValue('not_logged_str'));
}

if (Tools::getValue('modeResync') == 'resyncProductsAndImages') {
    EbayProductImage::removeAllProductImage();
}

$ebay_profile = new EbayProfile(Tools::getValue('id_ebay_profile'));
$products = EbayProduct::getProductsIdForSync($ebay_profile->id);
while ($product_id = $products->fetch(PDO::FETCH_ASSOC)) {
    $product = new Product($product_id['id_product'], false, $ebay_profile->id_lang);
    EbayTaskManager::deleteTaskForPorductAndEbayProfile($product_id['id_product'], $ebay_profile->id);
    EbayTaskManager::addTask('update', $product, null, $ebay_profile->id);
}
die();
