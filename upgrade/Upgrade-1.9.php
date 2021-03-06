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
function upgrade_module_1_9($module)
{
    $sql = [];
    include dirname(__FILE__) . '/sql/sql-upgrade-1-9.php';

    if (!empty($sql) && is_array($sql)) {
        foreach ($sql as $request) {
            if (!Db::getInstance()->execute($request)) {
                return false;
            }
        }
    }

    if (!Configuration::get('EBAY_ORDERS_DAYS_BACKWARD')) {
        Configuration::updateValue('EBAY_ORDERS_DAYS_BACKWARD', 30, false, 0, 0);
    }

    if (!Configuration::get('EBAY_LOGS_DAYS')) {
        Configuration::updateValue('EBAY_LOGS_DAYS', 30, false, 0, 0);
    }

    $hook_update_order_status = version_compare(_PS_VERSION_, '1.5', '>') ? 'actionOrderStatusUpdate' : 'updateOrderStatus';
    if (!$module->registerHook($hook_update_order_status)) {
        return false;
    }

    return true;
}
