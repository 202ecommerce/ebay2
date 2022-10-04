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

class EbayBestOffers extends ObjectModel
{
    public $id_ebay_profile;

    public $itemid;

    public $id_product;

    public $id_best_offer;

    public $id_product_attribute;

    public $date_add;

    public $seller_message;

    public $status;

    public $expirationTime;

    public $price;

    public $quantity;

    public $product_title;

    public $best_offer_ebay_id;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition;

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        self::$definition = [
            'table' => 'ebay_best_offers',
            'primary' => 'id_best_offer',
            'fields' => [
                'id_ebay_profile' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
                'itemid' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                'product_title' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                'best_offer_ebay_id' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
                'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
                'id_product_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
                'seller_message' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                'status' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                'expirationTime' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
                'price' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat'],
                'quantity' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            ],
        ];

        $this->date_add = date('Y-m-d H:i:s');

        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function get($offset, $limit)
    {
        return Db::getInstance()->executeS('SELECT *
			FROM `' . _DB_PREFIX_ . 'ebay_best_offers`
			ORDER BY `id_best_offer` DESC
			LIMIT ' . (int) $offset . ', ' . (int) $limit);
    }

    public static function count()
    {
        return Db::getInstance()->getValue('SELECT count(*)
			FROM `' . _DB_PREFIX_ . 'ebay_best_offers`');
    }

    public static function clear()
    {
        return Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_best_offers');
    }

    public static function getOffersForProfile($id_ebay_profile)
    {
        return Db::getInstance()->executeS('SELECT *
			FROM `' . _DB_PREFIX_ . 'ebay_best_offers`
			WHERE id_ebay_profile = ' . (int) $id_ebay_profile . '
			ORDER BY `id_best_offer` DESC');
    }

    public static function getOffers($id_ebay_profile, $page_current = false, $length = false, $search = false)
    {
        if ($page_current && $length) {
            $limit = (int) $length;
            $offset = $limit * ((int) $page_current - 1);
            $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'ebay_best_offers 
                       WHERE id_ebay_profile = ' . (int) $id_ebay_profile;
            if ($search['id_product']) {
                $query .= " AND id_product LIKE '%" . (int) $search['id_product'] . "%'";
            }
            if ($search['id_product_attribute']) {
                $query .= " AND id_product_attribute LIKE '%" . (int) $search['id_product_attribute'] . "%'";
            }
            if ($search['status']) {
                $query .= " AND status LIKE '%" . pSQL($search['status']) . "%'";
            }
            if ($search['date']) {
                $query .= " AND date_add LIKE '%" . pSQL($search['date']) . "%'";
            }
            if ($search['itemid']) {
                $query .= " AND itemid LIKE '%" . pSQL($search['itemid']) . "%'";
            }

            $query .= ' ORDER BY `date_add` LIMIT ' . pSQL($limit) . '  OFFSET ' . (int) $offset;

            return Db::getInstance()->ExecuteS($query);
        } else {
            $sql_select = 'SELECT * FROM `' . _DB_PREFIX_ . 'ebay_best_offers` WHERE `id_ebay_profile` = ' . (int) $id_ebay_profile . ' ORDER BY `date_add`';

            return Db::getInstance()->executeS($sql_select);
        }
    }
}
