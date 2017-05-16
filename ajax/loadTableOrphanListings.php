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

include_once dirname(__FILE__).'/../../../config/config.inc.php';
include_once dirname(__FILE__).'/../../../init.php';
include_once dirname(__FILE__).'/../ebay.php';

if (version_compare(_PS_VERSION_, '1.5', '>=') && Tools::getValue('id_shop')) {
    $context = Context::getContext();
    $context->shop = new Shop((int) Tools::getValue('id_shop'));
}

if (!Configuration::get('EBAY_SECURITY_TOKEN')
    || Tools::getValue('token') != Configuration::get('EBAY_SECURITY_TOKEN')) {
    return Tools::safeOutput(Tools::getValue('not_logged_str'));
}

$final_res = EbayProduct::getOrphanListing(Tools::getValue('profile'));

$context = Context::getContext();

// Smarty
$template_vars = array(
    'id_ebay_profile' => Tools::getValue('profile'),
    'id_employee' => Tools::getValue('id_employee'),
    'ads' => $final_res,
    'count' => count($final_res),
);

// Smarty datas
$ebay = new Ebay();
$context->smarty->assign($template_vars);

echo $ebay->display(realpath(dirname(__FILE__).'/../'), '/views/templates/hook/table_orphan_listings.tpl');
