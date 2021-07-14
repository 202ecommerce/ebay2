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

class EbayDebugTools
{
    public static function updateProfileIdentifier($old, $new)
    {
        $return = true;
        $data = [
            'ebay_user_identifier' => $new
        ];

        $return &= Db::getInstance()->update(
            'ebay_profile',
            $data,
            sprintf('ebay_user_identifier LIKE "%s"', $old)
        );

        $return &= Db::getInstance()->update(
            'ebay_user_identifier_token',
            $data,
            sprintf('ebay_user_identifier LIKE "%s"', $old)
        );

        return $return;
    }

    public static function getTableData($table, $where = null)
    {
        $query = (new DbQuery())->from($table);

        if (false === is_null($where)) {
            $query->where($where);
        }

        return Db::getInstance()->executeS($query);
    }

    public static function describeTable($table)
    {
        $query = sprintf(
            'DESCRIBE %s',
            pSQL(_DB_PREFIX_ . $table)
        );

        return Db::getInstance()->executeS($query);
    }

    public static function showKeys($table)
    {
        $query = sprintf(
            'SHOW INDEX FROM %s',
            pSQL(_DB_PREFIX_ . $table)
        );

        return Db::getInstance()->executeS($query);
    }
}