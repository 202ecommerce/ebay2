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
class EbayShipping
{
    public static function getPsCarrierByEbayCarrier($id_ebay_profile, $ebay_carrier)
    {
        return Db::getInstance()->getValue('SELECT `ps_carrier`
            FROM `' . _DB_PREFIX_ . 'ebay_shipping`
            WHERE `id_ebay_profile` = ' . (int) $id_ebay_profile . '
            AND `ebay_carrier` = \'' . pSQL($ebay_carrier) . '\'');
    }

    public static function getNationalShippings($id_ebay_profile, $id_product = null)
    {
        $shippings = Db::getInstance()->ExecuteS('SELECT *
            FROM ' . _DB_PREFIX_ . 'ebay_shipping
            WHERE `id_ebay_profile` = ' . (int) $id_ebay_profile . '
            AND international = 0');

        //Check if product can be shipped because of weight or dimension
        if ($id_product) {
            $exclude = [];

            foreach ($shippings as $key => $value) {
                $carrier = new Carrier($value['ps_carrier']);
                $product = new Product($id_product);
                if ($carrier->range_behavior) {
                    if (($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT
                        && (!Carrier::checkDeliveryPriceByWeight($carrier->id, $product->weight, $value['id_zone'])))
                        || ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE
                            && (!Carrier::checkDeliveryPriceByPrice($carrier->id, $product->getPrice(), $value['id_zone'], Configuration::get('PS_CURRENCY_DEFAULT'))))) {
                        $exclude[] = $key;
                    }
                }

                if (($carrier->max_width < $product->width && $carrier->max_width != 0) || ($carrier->max_height < $product->height && $carrier->max_height != 0) || ($carrier->max_depth < $product->depth && $carrier->max_depth != 0)) {
                    $exclude[] = $key;
                    continue;
                }
            }

            $exclude = array_unique($exclude);
            foreach ($exclude as $key_to_exclude) {
                unset($shippings[$key_to_exclude]);
            }
        }

        if ($id_product) {
            $shippings_product = Db::getInstance()->ExecuteS('SELECT c.id_carrier as ps_carrier
            FROM ' . _DB_PREFIX_ . 'carrier c
            LEFT JOIN `' . _DB_PREFIX_ . 'product_carrier` pc ON c.`id_reference` = pc.`id_carrier_reference`
            WHERE c.deleted = 0 AND pc.id_product = ' . (int) $id_product);
            if (count($shippings_product) > 0) {
                $intersect_shippings = self::arrayIntersectAssocField($shippings, $shippings_product, 'ps_carrier');
                if ($intersect_shippings) {
                    $shippings = $intersect_shippings;
                }
            }
        }

        // Sort a shippings beacause FR_RemiseEnMainPropre should be last
        usort($shippings, ['self', 'sortCarriers']);

        return $shippings;
    }

    public static function sortCarriers($carrierOne, $carrierTwo)
    {
        if ($carrierOne['ebay_carrier'] == 'FR_RemiseEnMainPropre') {
            return 1;
        }

        return -1;
    }

    public static function internationalShippingsHaveZone($shippings)
    {
        foreach ($shippings as $shipping) {
            if (!Db::getInstance()->getValue('SELECT * FROM ' . _DB_PREFIX_ . 'ebay_shipping_international_zone WHERE id_ebay_shipping = ' . (int) $shipping['id_ebay_shipping'])) {
                return false;
            }
        }

        return true;
    }

    public static function getInternationalShippings($id_ebay_profile, $id_product = null)
    {
        $shippings = Db::getInstance()->ExecuteS('SELECT *
            FROM ' . _DB_PREFIX_ . 'ebay_shipping
            WHERE `id_ebay_profile` = ' . (int) $id_ebay_profile . '
            AND international = 1');

        //Check if product can be shipped because of weight or dimension
        if ($id_product) {
            $exclude = [];

            foreach ($shippings as $key => $value) {
                $carrier = new Carrier($value['ps_carrier']);
                $product = new Product($id_product);
                if ($carrier->range_behavior) {
                    if (($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT
                        && (!Carrier::checkDeliveryPriceByWeight($carrier->id, $product->weight, $value['id_zone'])))
                        || ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE
                            && (!Carrier::checkDeliveryPriceByPrice($carrier->id, $product->getPrice(), $value['id_zone'], Configuration::get('PS_CURRENCY_DEFAULT'))))) {
                        $exclude[] = $key;
                    }
                }

                if (($carrier->max_width < $product->width && $carrier->max_width != 0) || ($carrier->max_height < $product->height && $carrier->max_height != 0) || ($carrier->max_depth < $product->depth && $carrier->max_depth != 0)) {
                    $exclude[] = $key;
                    continue;
                }
            }

            $exclude = array_unique($exclude);
            foreach ($exclude as $key_to_exclude) {
                unset($shippings[$key_to_exclude]);
            }
        }

        if ($id_product) {
            $shippings_product = Db::getInstance()->ExecuteS('SELECT c.id_carrier as ps_carrier
            FROM ' . _DB_PREFIX_ . 'carrier c
            LEFT JOIN `' . _DB_PREFIX_ . 'product_carrier` pc ON c.`id_reference` = pc.`id_carrier_reference`
            WHERE c.deleted = 0 AND pc.id_product = ' . (int) $id_product);
            if (count($shippings_product) > 0) {
                $intersect_shippings = self::arrayIntersectAssocField($shippings, $shippings_product, 'ps_carrier');
                if ($shippings_product) {
                    $shippings = $intersect_shippings;
                }
            }
        }

        // Sort a shippings beacause FR_RemiseEnMainPropre should be last
        usort($shippings, ['self', 'sortCarriers']);

        return $shippings;
    }

    public static function arrayIntersectAssocField($array1, $array2, $key_to_test)
    {
        $array_result = $array1;
        $array_tmp = [];
        foreach ($array2 as $item) {
            if (isset($item[$key_to_test])) {
                $array_tmp[] = $item[$key_to_test];
            }
        }
        foreach ($array_result as $key => $value) {
            if (!in_array($value[$key_to_test], $array_tmp)) {
                unset($array_result[$key]);
            }
        }

        return $array_result;
    }

    public static function getNbNationalShippings($id_ebay_profile)
    {
        return Db::getInstance()->getValue('SELECT count(*)
            FROM ' . _DB_PREFIX_ . 'ebay_shipping
            WHERE `international` = 0
            AND `id_ebay_profile` = ' . (int) $id_ebay_profile);
    }

    public static function getNbInternationalShippings($id_ebay_profile)
    {
        return Db::getInstance()->getValue('SELECT count(*)
            FROM ' . _DB_PREFIX_ . 'ebay_shipping
            WHERE international = 1
            AND `id_ebay_profile` = ' . (int) $id_ebay_profile);
    }

    public static function insert($id_ebay_profile, $ebay_carrier, $ps_carrier, $extra_fee, $id_zone, $international = false)
    {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'ebay_shipping` (
            `id_ebay_profile`,
            `ebay_carrier`,
            `ps_carrier`,
            `extra_fee`,
            `international`,
            `id_zone`
            )
            VALUES(
            \'' . (int) $id_ebay_profile . '\',
            \'' . pSQL($ebay_carrier) . '\',
            \'' . (int) $ps_carrier . '\',
            \'' . (float) $extra_fee . '\',
            \'' . (int) $international . '\',
            \'' . (int) $id_zone . '\')';

        Db::getInstance()->Execute($sql);
    }

    public static function truncateNational($id_ebay_profile)
    {
        return Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_shipping
            WHERE `international` = 0 AND `id_ebay_profile` = ' . (int) $id_ebay_profile);
    }

    public static function truncateInternational($id_ebay_profile)
    {
        return Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_shipping
            WHERE `international` = 1 AND `id_ebay_profile` = ' . (int) $id_ebay_profile);
    }

    public static function getLastShippingId($id_ebay_profile)
    {
        return Db::getInstance()->getValue('SELECT id_ebay_shipping
            FROM ' . _DB_PREFIX_ . 'ebay_shipping
            WHERE `id_ebay_profile` = ' . (int) $id_ebay_profile . '
            ORDER BY id_ebay_shipping DESC');
    }

    public static function updatePsCarrier($old_ps_carrier, $new_ps_carrier)
    {
        return Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'ebay_shipping`
                SET `ps_carrier` = ' . (int) $new_ps_carrier . '
                WHERE `ps_carrier` = ' . (int) $old_ps_carrier);
    }
}
