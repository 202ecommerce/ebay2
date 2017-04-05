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
 *  International Registered Trademark & Property of PrestaShop SA
 */

class EbayBussinesPolicies
{

    public static function getPoliciesbyType($type, $id_ebay_profile)
    {
        return Db::getInstance()->executeS('SELECT name, id_bussines_Policie FROM ' . _DB_PREFIX_ . 'ebay_business_policies WHERE type ="' . pSQl($type) . '" AND id_ebay_profile=' . (int)$id_ebay_profile);

    }

    public static function setBussinesPolicies($id_ebay_profile, $var)
    {
        EbayConfiguration::set($id_ebay_profile, $var['type'], $var['id_bussines_Policie']);

    }

    public static function getPoliciesConfigurationbyIdCategory($id_category, $id_ebay_profile)
    {
        return Db::getInstance()->executeS('SELECT id_return, id_payment FROM ' . _DB_PREFIX_ . 'ebay_category_business_config WHERE id_category ="' . (int)$id_category . '" AND id_ebay_profile=' . (int)$id_ebay_profile);

    }

    public static function getPoliciesbyID($id_bussines_Policie, $id_ebay_profile)
    {
        return Db::getInstance()->executeS('SELECT name FROM ' . _DB_PREFIX_ . 'ebay_business_policies WHERE id_bussines_Policie ="' . pSQL($id_bussines_Policie) . '" AND id_ebay_profile=' . (int)$id_ebay_profile);

    }

    public static function getPoliciesbyName($name, $id_ebay_profile)
    {
        return Db::getInstance()->executeS('SELECT id, name, id_bussines_Policie FROM ' . _DB_PREFIX_ . 'ebay_business_policies WHERE name ="' . pSQL($name) . '" AND id_ebay_profile=' . (int)$id_ebay_profile);

    }

    public static function deletePoliciesConfgbyidCategories($id_ebay_profile, $id_category)
    {
        return Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_category_business_config WHERE id_ebay_profile = "' . (int)$id_ebay_profile . '" AND id_category ="' . (int)$id_category . '"');

    }

    public static function addPolicies($data, $id_ebay_profile)
    {
        Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'ebay_business_policies (`type`, `name`, `id_bussines_Policie`, `id_ebay_profile`) VALUES ("' . pSQl($data['ProfileType']) . '","' . pSQl($data['ProfileName']) . '","' . pSQl($data['ProfileID']) . '",' . (int)$id_ebay_profile . ')');
        return true;
    }

    public function resetBussinesPolicies($id_ebay_profile)
    {
        if (Db::getInstance()->execute('DELETE FROM' . _DB_PREFIX_ . 'ebay_category_business_config WHERE id_ebay_profile=' . (int)$id_ebay_profile)) {
            EbayRequest::importBusinessPolicies();
        } else {
            return false;
        }

    }

    public static function addShipPolicies($data, $id_ebay_profile)
    {
         Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'ebay_business_policies (`type`, `name`, `id_ebay_profile`) VALUES ("' . pSQl($data['ProfileType']) . '","' . pSQl($data['ProfileName']) . '","' . (int)$id_ebay_profile . '")');

        return Db::getInstance()->Insert_ID();
    }

    public static function updateShipPolicies($data, $id_ebay_profile)
    {
        return  Db::getInstance()->update('ebay_business_policies', array('id_bussines_Policie' => pSQL($data['id_bussines_Policie'])), 'id = '.(int)$data['id']);
    }
}
