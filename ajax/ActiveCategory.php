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

include dirname(__FILE__).'/../../../config/config.inc.php';
include_once dirname(__FILE__).'/../../../init.php';
include '../ebay.php';

if (!Tools::getValue('token') || Tools::getValue('token') != Configuration::get('EBAY_SECURITY_TOKEN')) {
    die('ERROR: Invalid Token');
}

$category_conf = EbayCategoryConfiguration::getConfigByCategoryId(Tools::getValue('profile'), Tools::getValue('ebay_category'));

if ($category_conf[0]['sync']) {
    $value =0;
} else {
    $value =1;
}

EbayCategoryConfiguration::updateByIdProfileAndIdCategory(Tools::getValue('profile'), Tools::getValue('ebay_category'), $data = array('sync' => $value));

$sql = 'SELECT p.`id_product`
            FROM `'._DB_PREFIX_.'product` p';

$sql .= Shop::addSqlAssociation('product', 'p');
$sql .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (p.`id_product` = pl.`id_product`
                AND pl.`id_lang` = '.(int) Tools::getValue('ebay_category');
$sql .= Shop::addSqlRestrictionOnLang('pl');
$sql .= ')
            LEFT JOIN `'._DB_PREFIX_.'ebay_product_configuration` epc
                ON p.`id_product` = epc.`id_product` AND epc.id_ebay_profile = '.(int)Tools::getValue('profile').'
            LEFT JOIN `'._DB_PREFIX_.'stock_available` sa
                ON p.`id_product` = sa.`id_product`
                AND sa.`id_product_attribute` = 0
            WHERE ';
$sql .= ' product_shop.`id_category_default` = '.(int) Tools::getValue('ebay_category');
$sql .= StockAvailable::addSqlShopRestriction(null, null, 'sa');

$to_synchronize_product_ids = Db::getInstance()->ExecuteS($sql);

if ($value) {
    foreach ($to_synchronize_product_ids as $product_id_to_sync) {
        $product = new Product($product_id_to_sync['id_product']);
        EbayTaskManager::addTask('add', $product, Tools::getValue('id_employee'), Tools::getValue('profile'));
    }
} else {
    foreach ($to_synchronize_product_ids as $product_id_to_sync) {
        EbayTaskManager::deleteTaskForPorductAndEbayProfile($product_id_to_sync['id_product'], Tools::getValue('profile'));
    }
}

echo count(EbayProduct::getOrphanListing(Tools::getValue('profile')));
