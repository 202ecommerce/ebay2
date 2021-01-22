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

class EbayOrderErrors extends ObjectModel
{

    public $error;
    public $id_order_seller;
    public $id_order_ebay;
    public $date_order;
    public $email;
    public $total;
    public $ebay_user_identifier;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'ebay_order_errors',
        'primary' => 'id_ebay_order_error',
        'fields' => array(
            'error' => array('type' => 'TYPE_STRING', 'validate' => 'isString'),
            'id_order_seller' => array('type' => 'TYPE_INT', 'validate' => 'isInt'),
            'id_order_ebay' => array('type' => 'TYPE_STRING', 'validate' => 'isString'),
            'total' => array('type' => 'TYPE_INT', 'validate' => 'isInt'),
            'email' => array('type' => 'TYPE_STRING', 'validate' => 'isString'),
            'ebay_user_identifier' => array('type' => 'TYPE_STRING', 'validate' => 'isString'),
            'date_order' => array('type' => 'TYPE_DATE', 'validate' => 'isDateFormat'),
            'date_add' => array('type' => 'TYPE_DATE', 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => 'TYPE_DATE', 'validate' => 'isDateFormat'),
        ),
    );

    public static function getIds()
    {
        $sql = "SELECT `".self::$definition['primary']."` FROM "._DB_PREFIX_.self::$definition['table']."";
        $objsIDs = Db::getInstance()->ExecuteS($sql);
        return $objsIDs;
    }

    public static function install()
    {
        $sql = array();
        // Create Category Table in Database
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$definition['table'].'` (
				  	`'.self::$definition['primary'].'` int(16) NOT NULL AUTO_INCREMENT,
				 	`error` varchar(255) NOT NULL,
				 	`id_order_seller` int(16) NOT NULL,
				 	`id_order_ebay`  varchar(255) NOT NULL,
				 	`total` int(16) NOT NULL,
				 	`email` varchar(255),
				 	`date_order` datetime NOT NULL,
				 	`ebay_user_identifier` varchar(255) NOT NULL,
				 	date_add datetime NOT NULL,
					date_upd datetime NOT NULL,
					UNIQUE(`'.self::$definition['primary'].'`),
				  	PRIMARY KEY  ('.self::$definition['primary'].')
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        foreach ($sql as $q) {
            Db::getInstance()->Execute($q);
        }
    }

    public static function uninstall()
    {
        $sql= array();
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.self::$definition['table'].'`';

        foreach ($sql as $q) {
            Db::getInstance()->Execute($q);
        }
    }

    public static function truncate()
    {
        $q = 'TRUNCATE `'._DB_PREFIX_.self::$definition['table'].'`';

        Db::getInstance()->Execute($q);
    }

    public static function getEbayOrdersCountry()
    {
        $q = 'SELECT * FROM `'._DB_PREFIX_.self::$definition['table'].'`';

        $result = array();

        if ($rows = Db::getInstance()->ExecuteS($q)) {
            foreach ($rows as $row) {
                $error = Tools::jsonDecode($row['error']);
                if ($error->type == 'country') {
                    $result[$error->iso_code][] = $row;
                }
                return $result;
            }
        } else {
            return false;
        }
    }

    public static function getAll($id_ebay_profile)
    {
        $ebay_profile = new EbayProfile($id_ebay_profile);
        $q = 'SELECT * FROM `'._DB_PREFIX_.self::$definition['table'].'` WHERE `ebay_user_identifier` = "'.pSQL($ebay_profile->ebay_user_identifier).'"';
        return Db::getInstance()->ExecuteS($q);
    }

    public static function getAllCount($id_ebay_profile)
    {
        $ebay_profile = new EbayProfile($id_ebay_profile);
        $q = 'SELECT COUNT(*) AS nb FROM `'._DB_PREFIX_.self::$definition['table'].'` WHERE `ebay_user_identifier` = "'.pSQL($ebay_profile->ebay_user_identifier).'"';
        return Db::getInstance()->ExecuteS($q);
    }

    public static function deleteByOrderRef($id_order_ref)
    {
        $q = 'DELETE FROM `'._DB_PREFIX_.self::$definition['table'].'` WHERE `id_order_ebay` = "'.pSQL($id_order_ref).'"';

        return Db::getInstance()->Execute($q);
    }

    public static function getErrorByOrderRef($id_order_ref)
    {
        $q = 'SELECT * FROM `'._DB_PREFIX_.self::$definition['table'].'` WHERE `id_order_ebay` = "'.pSQL($id_order_ref).'"';

        return Db::getInstance()->ExecuteS($q);
    }
}
