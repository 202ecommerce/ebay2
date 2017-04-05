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
 * International Registered Trademark & Property of PrestaShop SA
 */

class EbayDeliveryTimeOptions
{
    public static function getAll()
    {
        return Db::getInstance()->ExecuteS('SELECT *
			FROM '._DB_PREFIX_.'ebay_delivery_time_options');
    }

    public static function getTotal()
    {
        return Db::getInstance()->getValue('SELECT COUNT(*) AS nb
			FROM '._DB_PREFIX_.'ebay_delivery_time_options');
    }

    public static function insert($all_data)
    {
        $to_insert = array();
        if (is_array($all_data) && count($all_data)) {
            foreach ($all_data as $key => $data) {
                $to_insert[bqSQL($key)] = pSQL($data);
            }
        }

        Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_delivery_time_options', $to_insert, 'INSERT');
    }

    public static function getDeliveryTimeOptions()
    {
        if (EbayDeliveryTimeOptions::getTotal()) {
            return EbayDeliveryTimeOptions::getAll();
        }

        $ebay = new EbayRequest();
        $delivery_time_options = $ebay->getDeliveryTimeOptions();

        foreach ($delivery_time_options as $delivery_time_option) {
            EbayDeliveryTimeOptions::insert(array_map('pSQL', $delivery_time_option));
        }

        return $delivery_time_options;
    }
}
