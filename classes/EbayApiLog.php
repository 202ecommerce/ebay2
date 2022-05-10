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

class EbayApiLog extends ObjectModel
{
    public $id_ebay_profile;
    public $type;
    public $context;
    public $data_sent;
    public $response;
    public $request;

    public $id_product;
    public $status;
    public $id_product_attribute;
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition;

    // for Prestashop 1.4
    protected $tables;
    protected $fieldsRequired;
    protected $fieldsSize;
    protected $fieldsValidate;
    protected $table = 'ebay_api_log';
    protected $identifier = 'id_ebay_api_log';

    public function getFields()
    {
        $fields = [];
        parent::validateFields();
        if (isset($this->id)) {
            $fields['id_ebay_api_log'] = (int) ($this->id);
        }

        $fields['id_ebay_profile'] = (int) ($this->id_ebay_profile);
        $fields['type'] = pSQL($this->type);
        $fields['context'] = pSQL($this->context);
        $fields['data_sent'] = pSQL($this->data_sent);
        $fields['response'] = pSQL($this->response, true);
        $fields['request'] = pSQL($this->request, true);
        $fields['id_product'] = (int) $this->id_product;
        $fields['status'] = pSQL($this->status);
        $fields['date_add'] = pSQL($this->date_add);
        $fields['id_product_attribute'] = pSQL($this->id_product_attribute);

        return $fields;
    }

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        self::$definition = [
                'table' => 'ebay_api_log',
                'primary' => 'id_ebay_api_log',
                'fields' => [
                    'id_ebay_profile' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
                    'type' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                    'context' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                    'data_sent' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                    'response' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                    'request' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                    'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
                    'status' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                    'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
                    'id_product_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
                ],
            ];

        $this->date_add = date('Y-m-d H:i:s');

        return parent::__construct($id, $id_lang, $id_shop);
    }

    public static function get($offset, $limit)
    {
        return Db::getInstance()->executeS('SELECT *
			FROM `' . _DB_PREFIX_ . 'ebay_api_log`
			ORDER BY `id_ebay_api_log` DESC
			LIMIT ' . (int) $offset . ', ' . (int) $limit);
    }

    public static function count()
    {
        return Db::getInstance()->getValue('SELECT count(*)
			FROM `' . _DB_PREFIX_ . 'ebay_api_log`');
    }

    public static function cleanOlderThan($nb_days)
    {
        $date = date('Y-m-d\TH:i:s', strtotime('-1 day'));

        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ebay_api_log`
			WHERE `date_add` < \'' . pSQL($date) . '\'');
    }

    public static function clear()
    {
        return Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_api_log');
    }

    public static function getLogsForProfile($id_ebay_profile)
    {
        return Db::getInstance()->executeS('SELECT *
			FROM `' . _DB_PREFIX_ . 'ebay_api_log`
			WHERE id_ebay_profile = ' . (int) $id_ebay_profile . '
			ORDER BY `id_ebay_api_log` DESC');
    }

    public static function getApilogs($id_ebay_profile, $page_current = false, $length = false, $search = false)
    {
        if ($page_current && $length) {
            $limit = (int) $length;
            $offset = $limit * ((int) $page_current - 1);
            $query = self::getAllApiLogsQueryBySearch($id_ebay_profile, $search);
            $query .= ' ORDER BY `date_add` LIMIT ' . pSQL($limit) . '  OFFSET ' . (int) $offset;
            //var_dump($query); die();
            return DB::getInstance()->ExecuteS($query);
        } else {
            $sql_select = 'SELECT * FROM `' . _DB_PREFIX_ . 'ebay_api_log` WHERE `id_ebay_profile` = ' . pSQL($id_ebay_profile) . ' ORDER BY `date_add`';

            return DB::getInstance()->executeS($sql_select);
        }
    }

    private static function getAllApiLogsQueryBySearch($id_ebay_profile, $search)
    {
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'ebay_api_log 
                       WHERE id_ebay_profile = ' . (int) $id_ebay_profile;
        if (false == empty($search['id_product'])) {
            $query .= " AND id_product LIKE '%" . (int) $search['id_product'] . "%'";
        }
        if (false == empty($search['id_product_attribute'])) {
            $query .= " AND id_product_attribute LIKE '%" . (int) $search['id_product_attribute'] . "%'";
        }
        if (false == empty($search['status'])) {
            $query .= " AND status LIKE '%" . pSQL(trim($search['status'])) . "%'";
        }
        if (false == empty($search['type'])) {
            $query .= " AND type LIKE '%" . pSQL(trim($search['type'])) . "%'";
        }
        if (false == empty($search['date'])) {
            $query .= " AND date_add LIKE '%" . pSQL($search['date']) . "%'";
        }

        return $query;
    }

    public static function getAllApiLogsSearchCount($id_ebay_profile, $search)
    {
        $query = self::getAllApiLogsQueryBySearch($id_ebay_profile, $search);
        $result = DB::getInstance()->ExecuteS($query);

        return count($result);
    }
}
