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
 *  @copyright 2007-2020 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @param Ebay $module
 * @return bool
 */
function upgrade_module_2_1_5($module)
{
    $result = true;
    $count = (int)DB::getInstance()->getValue('SELECT count(*) 
	    FROM INFORMATION_SCHEMA.COLUMNS
		WHERE `TABLE_NAME` = "'._DB_PREFIX_.'ebay_task_manager"
		AND `TABLE_SCHEMA` = "'._DB_NAME_.'"
		AND `COLUMN_NAME` = "priority"');
    if ($count == 0) {
        $alter = 'ALTER TABLE ' . _DB_PREFIX_ . 'ebay_task_manager ADD COLUMN `priority` INT(2)';
        $result &= DB::getInstance()->execute($alter);
    }

    $alter_2 = 'ALTER TABLE ' . _DB_PREFIX_ . 'ebay_task_manager MODIFY `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
    $result &= DB::getInstance()->execute($alter_2);

    $select = 'show index
	    FROM '._DB_PREFIX_.'ebay_task_manager
	    where Key_name = "unique_task"';

    if (empty(DB::getInstance()->executeS($select))) {
        $alter_3 = "ALTER TABLE " . _DB_PREFIX_ . "ebay_task_manager 
                        ADD UNIQUE KEY unique_task(id_product, id_product_attribute, id_task, id_ebay_profile)";
        $result &= DB::getInstance()->execute($alter_3);
    }

    $count = (int)DB::getInstance()->getValue('SELECT count(*) 
	    FROM INFORMATION_SCHEMA.COLUMNS
		WHERE `TABLE_NAME` = "'._DB_PREFIX_.'ebay_category"
		AND `TABLE_SCHEMA` = "'._DB_NAME_.'"
		AND `COLUMN_NAME` = "best_offer_enabled"');
    if ($count == 0) {
        $result &= DB::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'ebay_category` ADD COLUMN `best_offer_enabled` tinyint(1)');
    }

    return $result;
}
