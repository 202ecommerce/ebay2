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

class EbayShippingInternationalZone
{
    public static function getIdEbayZonesByIdEbayShipping($id_ebay_profile, $id_ebay_shipping)
    {
        return Db::getInstance()->ExecuteS('SELECT id_ebay_zone
			FROM '._DB_PREFIX_.'ebay_shipping_international_zone
			WHERE `id_ebay_profile` = '.(int) $id_ebay_profile.'
			AND id_ebay_shipping = "'.(int) $id_ebay_shipping.'"');
    }

    public static function insert($id_ebay_profile, $id_ebay_shipping, $id_ebay_zone)
    {
        $sql = 'INSERT INTO '._DB_PREFIX_.'ebay_shipping_international_zone(`id_ebay_shipping`, `id_ebay_zone`, `id_ebay_profile`)
			VALUES(\''.(int) $id_ebay_shipping.'\', \''.pSQL($id_ebay_zone).'\', \''.pSQL($id_ebay_profile).'\')';

        DB::getInstance()->Execute($sql);
    }

    public static function getExistingInternationalCarrier($id_ebay_profile)
    {
        $existing_international_carriers = EbayShipping::getInternationalShippings($id_ebay_profile);

        foreach ($existing_international_carriers as $key => &$carrier) {
            //get All shipping location associated
            $carrier['shippingLocation'] = DB::getInstance()->ExecuteS('SELECT *
				FROM '._DB_PREFIX_.'ebay_shipping_international_zone
				WHERE `id_ebay_profile` = '.(int) $id_ebay_profile.'
				AND id_ebay_shipping = \''.(int) $carrier['id_ebay_shipping'].'\'');
        }

        return $existing_international_carriers;
    }
}
