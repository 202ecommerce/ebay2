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
 * International Registered Trademark & Property of PrestaShop SA
 */

class EbayCategoryConditionConfiguration
{
    const PS_CONDITION_NEW = 1;
    const PS_CONDITION_USED = 2;
    const PS_CONDITION_REFURBISHED = 3;

    public static function getPSConditions($condition_type = null)
    {
        $condition_types = array(
            EbayCategoryConditionConfiguration::PS_CONDITION_NEW         => 'new',
            EbayCategoryConditionConfiguration::PS_CONDITION_USED        => 'used',
            EbayCategoryConditionConfiguration::PS_CONDITION_REFURBISHED => 'refurbished',
        );

        if ($condition_type) {
            return $condition_types[$condition_type];
        }

        return $condition_types;
    }

    public static function replace($data)
    {
        $to_insert = array();
        foreach ($data as $key => $value) {
            $to_insert[bqSQL($key)] = pSQL($value);
        }

        Db::getInstance()->insert('ebay_category_condition_configuration', $to_insert, false, false, Db::REPLACE);
    }
}
