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

class EbayShippingService
{
    public static function getAll($ebay_site_id)
    {
        return Db::getInstance()->ExecuteS('SELECT *
			FROM '._DB_PREFIX_.'ebay_shipping_service
			WHERE `ebay_site_id` = '.(int) $ebay_site_id);
    }

    public static function getTotal($ebay_site_id)
    {
        return Db::getInstance()->getValue('SELECT COUNT(*) AS nb
			FROM '._DB_PREFIX_.'ebay_shipping_service
			WHERE `ebay_site_id` = '.(int) $ebay_site_id);
    }

    public static function insert($data)
    {
        return Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_shipping_service', $data, 'INSERT');
    }

    public static function getCarriers($ebay_site_id)
    {
        if (EbayShippingService::getTotal($ebay_site_id)) {
            return EbayShippingService::getAll($ebay_site_id);
        }

        $ebay = new EbayRequest();
        $carriers = $ebay->getCarriers();

        foreach ($carriers as $carrier) {
            EbayShippingService::insert(array_map('pSQL', $carrier));
        }

        return $carriers;
    }

    public static function getCarrierByName($name, $ebay_site_id)
    {
        
        return Db::getInstance()->ExecuteS('SELECT *
			FROM '._DB_PREFIX_.'ebay_shipping_service
			WHERE `ebay_site_id` = '.(int) $ebay_site_id .' AND `shippingService` = "'. pSQL($name) .'"');
    }
}
