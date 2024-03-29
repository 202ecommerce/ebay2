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
class EbayOrderLog extends ObjectModel
{
    public $id_ebay_profile;
    public $id_ebay_order;
    public $id_orders;
    public $type;
    public $success;
    public $data;

    public $date_add;
    public $date_update;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition;

    // for Prestashop 1.4
    protected $tables;
    protected $fieldsRequired;
    protected $fieldsSize;
    protected $fieldsValidate;
    protected $table = 'ebay_order_log';
    protected $identifier = 'id_ebay_order_log';

    public function getFields()
    {
        $fields = [];
        parent::validateFields();
        if (isset($this->id)) {
            $fields['id_ebay_order_log'] = (int) ($this->id);
        }

        $fields['id_ebay_profile'] = (int) ($this->id_ebay_profile);
        $fields['id_ebay_order'] = (int) ($this->id_ebay_order);
        $fields['id_orders'] = pSQL($this->id_orders);
        $fields['type'] = pSQL($this->type);
        $fields['success'] = (int) ($this->success);
        $fields['data'] = pSQL($this->data);

        $fields['date_add'] = pSQL($this->date_add);
        $fields['date_update'] = pSQL($this->date_update);

        return $fields;
    }

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        self::$definition = [
                'table' => 'ebay_order_log',
                'primary' => 'id_ebay_order_log',
                'fields' => [
                    'id_ebay_profile' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
                    'id_ebay_order' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
                    'id_orders' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                    'type' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                    'success' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
                    'data' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                    'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
                    'date_update' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
                ],
            ];

        $this->date_add = date('Y-m-d H:i:s');

        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function get($offset, $limit)
    {
        return Db::getInstance()->executeS('SELECT *
			FROM `' . _DB_PREFIX_ . 'ebay_order_log`
			ORDER BY `id_ebay_order_log` DESC
			LIMIT ' . (int) $offset . ', ' . (int) $limit);
    }

    public static function count()
    {
        return Db::getInstance()->getValue('SELECT count(*)
			FROM `' . _DB_PREFIX_ . 'ebay_order_log`');
    }

    public static function cleanOlderThan($nb_days)
    {
        $date = date('Y-m-d\TH:i:s', strtotime('-1 day'));

        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ebay_order_log`
			WHERE `date_add` < \'' . pSQL($date) . '\'');
    }
}
