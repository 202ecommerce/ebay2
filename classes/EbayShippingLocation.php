<?php
/**
 * 2007-2021 PrestaShop
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
 * @copyright Copyright (c) 2007-2021 202-ecommerce
 * @license Commercial license
 *  International Registered Trademark & Property of PrestaShop SA
 */

class EbayShippingLocation
{
    public static function getEbayShippingLocations($idEbayProfile = null)
    {
        $ebayProfile = $idEbayProfile ? new EbayProfile($idEbayProfile) : EbayProfile::getCurrent();
        $query = new DbQuery();
        $query->from('ebay_shipping_location');
        $query->select('*');
        $query->where('ebay_site_id = ' . pSQL($ebayProfile->ebay_site_id));
        $query->groupBy('`description`');

        return Db::getInstance()->ExecuteS($query);
    }

    public static function getTotal()
    {
        return Db::getInstance()->getValue('SELECT COUNT(*) AS nb
			FROM '._DB_PREFIX_.'ebay_delivery_time_options');
    }

    public static function insert($data)
    {
        $dbEbay = new DbEbay();
        $dbEbay->setDb(Db::getInstance());

        return $dbEbay->autoExecute(_DB_PREFIX_.'ebay_shipping_location', $data, 'REPLACE');
    }

    public static function getInternationalShippingLocations($idEbayProfile = null)
    {
        if (EbayShippingLocation::isInternationalShippingLocationExists($idEbayProfile)) {
            return EbayShippingLocation::getEbayShippingLocations();
        }

        $ebay = new EbayRequest();
        $locations = $ebay->getInternationalShippingLocations();
        $ebayProfile = $idEbayProfile ? new EbayProfile($idEbayProfile) : EbayProfile::getCurrent();

        foreach ($locations as $location) {
            $location['ebay_site_id'] = $ebayProfile->ebay_site_id;
            EbayShippingLocation::insert(array_map('pSQL', $location));
        }

        return $locations;
    }

    public static function isInternationalShippingLocationExists($idEbayProfile = null)
    {
        $ebayProfile = $idEbayProfile ? new EbayProfile($idEbayProfile) : EbayProfile::getCurrent();
        if (!$ebayProfile) {
            return false;
        }

        $query = new DbQuery();
        $query->select('COUNT(*)');
        $query->from('ebay_shipping_location');
        $query->where('ebay_site_id = ' . pSQL($ebayProfile->ebay_site_id));
        $result = Db::getInstance()->getValue($query);

        return $result == false ? false : $result > 0;
    }
}
