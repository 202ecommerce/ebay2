<?php
/**
 *  2007-2022 PrestaShop
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 202-ecommerce <tech@202-ecommerce.com>
 *  @copyright Copyright (c) 2007-2022 202-ecommerce
 *  @license Commercial license
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @param Ebay $module
 *
 * @return bool
 */
function upgrade_module_1_12_3($module)
{
    $sql = sql_1_12_3();
    if (!empty($sql) && is_array($sql)) {
        foreach ($sql as $request) {
            if (!Db::getInstance()->execute($request)) {
                return false;
            }
        }
    }

    if ($module->ebay_profile) {
        $module->ebay_profile->setConfiguration('EBAY_SPECIFICS_LAST_UPDATE', null);
    }

    $module->setConfiguration('EBAY_VERSION', $module->version);

    return true;
}

function sql_1_12_3()
{
    $sql = [];
    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ebay_category_specific` ADD `is_reference` tinyint(1) NULL';
    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ebay_category_specific` ADD `is_ean` tinyint(1) NULL';
    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'ebay_category_specific` ADD `is_upc` tinyint(1) NULL';

    return $sql;
}
