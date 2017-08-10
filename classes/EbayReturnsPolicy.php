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

require_once dirname(__FILE__).'/EbayConfiguration.php';

class EbayReturnsPolicy
{
    public static function getAll($id_profile)
    {
        return Db::getInstance()->ExecuteS('SELECT *
			FROM '._DB_PREFIX_.'ebay_returns_policy WHERE `id_country`=(SELECT `ebay_site_id` FROM '._DB_PREFIX_.'ebay_profile WHERE `id_ebay_profile`='.$id_profile.')');
    }

    public static function getTotal($id_profile)
    {
        $returnspolicy = Db::getInstance()->getValue('SELECT COUNT(*) AS nb	FROM '._DB_PREFIX_.'ebay_returns_policy WHERE `id_country`=(SELECT `ebay_site_id` FROM '._DB_PREFIX_.'ebay_profile WHERE `id_ebay_profile`='.$id_profile.')');
        $returnsWithin = EbayConfiguration::get($id_profile, 'EBAY_RETURNS_WITHIN_VALUES');
        $returnsWhoPays = EbayConfiguration::get($id_profile, 'EBAY_RETURNS_WHO_PAYS_VALUES');
        return $returnspolicy && $returnsWithin && $returnsWhoPays;
    }

    public static function insert($id_profile, $data)
    {
        $dbEbay = new DbEbay();
        $dbEbay->setDb(Db::getInstance());
        $id_country = Db::getInstance()->getValue('SELECT `ebay_site_id` FROM '._DB_PREFIX_.'ebay_profile WHERE `id_ebay_profile`='.$id_profile);

        return $dbEbay->autoExecute(_DB_PREFIX_.'ebay_returns_policy', array_merge(array('id_country'=>(int)$id_country), $data), 'INSERT');
    }

    public static function getReturnsPolicies($id_profile)
    {
        // already in the DB
        if (EbayReturnsPolicy::getTotal($id_profile)) {
            return EbayReturnsPolicy::getAll($id_profile);
        }

        $ebay_request = new EbayRequest();
        $policiesDetails = $ebay_request->getReturnsPolicies();

        foreach ($policiesDetails['ReturnsAccepted'] as $returns_policy) {
            EbayReturnsPolicy::insert($id_profile, array_map('pSQL', $returns_policy));
        }

        $ReturnsWithin = array();
        foreach ($policiesDetails['ReturnsWithin'] as $returns_within) {
            $ReturnsWithin[] = array_map('pSQL', $returns_within);
        }
        Db::getInstance()->execute('delete
			FROM `'._DB_PREFIX_.'ebay_configuration`
			WHERE `id_ebay_profile` = '.(int) $id_profile.'
			AND `name` = "'.pSQL('EBAY_RETURNS_WITHIN_VALUES').'"');
        EbayConfiguration::set($id_profile, 'EBAY_RETURNS_WITHIN_VALUES', serialize($ReturnsWithin), false, 0, 0);

        $returnsWhoPays = array();
        foreach ($policiesDetails['ReturnsWhoPays'] as $returns_within) {
            $returnsWhoPays[] = array_map('pSQL', $returns_within);
        }
        Db::getInstance()->execute('delete
			FROM `'._DB_PREFIX_.'ebay_configuration`
			WHERE `id_ebay_profile` = '.(int) $id_profile.'
			AND `name` = "'.pSQL('EBAY_RETURNS_WHO_PAYS_VALUES').'"');
        EbayConfiguration::set($id_profile, 'EBAY_RETURNS_WHO_PAYS_VALUES', serialize($returnsWhoPays), false, 0, 0);

        return $policiesDetails['ReturnsAccepted'];
    }
}
