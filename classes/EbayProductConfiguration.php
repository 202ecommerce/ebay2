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
class EbayProductConfiguration
{
    public static function getByProductIdAndProfile($product_id, $id_ebay_profile)
    {
        if (!$product_id) {
            return;
        }

        return Db::getInstance()->getRow('SELECT `id_product`, `blacklisted`, `extra_images`
			FROM `' . _DB_PREFIX_ . 'ebay_product_configuration`
			WHERE `id_product` = ' . (int) $product_id . '
			AND `id_ebay_profile` = ' . (int) $id_ebay_profile);
    }

    public static function isblocked($id_ebay_profile, $product_id)
    {
        return Db::getInstance()->ExecuteS('SELECT  `blacklisted`, `extra_images`
			FROM `' . _DB_PREFIX_ . 'ebay_product_configuration`
			WHERE `id_product` = ' . (int) $product_id . '
			AND `id_ebay_profile` = ' . (int) $id_ebay_profile . '
			AND `blacklisted` = 1');
    }

    public static function getBlacklistedProductIdsQuery($id_ebay_profile)
    {
        return 'SELECT `id_product`
			FROM `' . _DB_PREFIX_ . 'ebay_product_configuration`
			WHERE `id_ebay_profile` = ' . (int) $id_ebay_profile . '
			AND `blacklisted` = 1';
    }

    public static function insertOrUpdate($product_id, $data)
    {
        if (!count($data)) {
            return;
        }

        $to_insert = [];
        $fields_strs = [];
        foreach ($data as $key => $value) {
            $to_insert[bqSQL($key)] = '"' . pSQL($value) . '"';
            $fields_strs[] = '`' . bqSQL($key) . '` = "' . pSQL($value) . '"';
        }

        if ($data['blacklisted']) {
            EbayTaskManager::deleteTaskForPorductAndEbayProfile($product_id, $data['id_ebay_profile']);
        }

        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'ebay_product_configuration` (`id_product`, `' . implode('`,`', array_keys($to_insert)) . '`)
			VALUES (' . (int) $product_id . ', ' . implode(',', $to_insert) . ')
			ON DUPLICATE KEY UPDATE ';

        $sql .= implode(',', $fields_strs);

        return Db::getInstance()->execute($sql);
    }
}
