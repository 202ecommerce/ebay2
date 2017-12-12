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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

require_once dirname(__FILE__) . '/../../../config/config.inc.php';
require_once dirname(__FILE__) . '/../ebay.php';

if (!Tools::getValue('token') || Tools::getValue('token') != Configuration::get('EBAY_SECURITY_TOKEN')) {
    die('ERROR : INVALID TOKEN');
}
if (Tools::getValue('id_shop')) {
    $context = Context::getContext();
    $context->shop = new Shop((int)Tools::getValue('id_shop'));
}
$page_current = Tools::getValue('page') ? Tools::getValue('page') : 1;
$length = Tools::getValue('length') ? Tools::getValue('length') : 20;

$search = array(
    'id_product' => Tools::getValue('id_prod'),
    'id_product_ebay' => Tools::getValue('id_prod_ebay'),
    'name_product' => Tools::getValue('name_prod'),
    'name_cat' => Tools::getValue('name_cat'),
);

$ebay = new eBay();
$ebay->displayEbayListingsAjax(Tools::getValue('admin_path'), $search, (int)Tools::getValue('id_employee'), $page_current, $length);
