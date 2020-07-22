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
 * @author 202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) 2017-2020 202-ecommerce
 * @license Commercial license
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @param Ebay $module
 * @return bool
 */
function upgrade_module_1_12_2($module)
{
    if (Configuration::get('EBAY_SYNCHRONIZE_EAN')) {
        $module->setConfiguration('EBAY_SYNCHRONIZE_EAN', 'EAN');
    }

    if ($module->ebay_profile) {
        $module->ebay_profile->setConfiguration('EBAY_SPECIFICS_LAST_UPDATE', null);
    }

    $module->setConfiguration('EBAY_VERSION', $module->version);

    return true;
}
