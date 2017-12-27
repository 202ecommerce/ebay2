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
$base_path = dirname(__FILE__).TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'..'.TMP_DS;
require_once dirname(__FILE__).TMP_DS.'..'.TMP_DS.'classes'.TMP_DS.'EbayTools.php';
require_once dirname(__FILE__).'/../classes/EbayProfile.php';
require_once dirname(__FILE__).'/../classes/EbayRequest.php';
if (EbayTools::getValue('admin_path')) {
    define('_PS_ADMIN_DIR_', realpath(dirname(__FILE__).TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'..'.TMP_DS).TMP_DS.EbayTools::getValue('admin_path').TMP_DS);
}

if (!Tools::getValue('token')
    || Tools::getValue('token') != Configuration::get('EBAY_SECURITY_TOKEN')) {
    die('ERROR : INVALID TOKEN');
}


$ebay_profile = new EbayProfile((int) Tools::getValue('profile'));

$request = new EbayRequest($ebay_profile->id);
$request->importBusinessPolicies(true);
$vars = array(
        'PAYEMENTS' =>EbayBussinesPolicies::getPoliciesbyType('PAYMENT', $ebay_profile->id),
        'RETURN_POLICY' => EbayBussinesPolicies::getPoliciesbyType('RETURN_POLICY', $ebay_profile->id),
    );

// Smarty datas
$context = Context::getContext();
$ebay = new Ebay();
$context->smarty->assign($vars);

echo $ebay->display(realpath(dirname(__FILE__).'/../'), '/views/templates/hook/bp_selects.tpl');
