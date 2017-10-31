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
require_once dirname(__FILE__).TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'init.php';

$ids_tasks = Tools::getValue('ids_tasks');
if ($ids_tasks == 'all') {
    if (Tools::getValue('type') == 'work') {
        DB::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."ebay_task_manager WHERE `locked` != 0");
    } else {
        DB::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."ebay_task_manager");
    }
} else {
    $ids_tasks = pSQL(implode(', ', $ids_tasks));
    DB::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."ebay_task_manager WHERE id IN ($ids_tasks)");
}

die();
