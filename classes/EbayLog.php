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
class EbayLog extends ObjectModel
{
    public $type;
    public $text;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition;

    // for Prestashop 1.4
    protected $tables;
    protected $fieldsRequired;
    protected $fieldsSize;
    protected $fieldsValidate;
    protected $table = 'ebay_log';
    protected $identifier = 'id_ebay_log';

    public function getFields()
    {
        $fields = [];
        parent::validateFields();
        if (isset($this->id)) {
            $fields['id_ebay_log'] = (int) ($this->id);
            $fields['text'] = pSQL($this->text);
            $fields['type'] = pSQL($this->type);
            $fields['date_add'] = date('Y-m-d H:i:s');

            return $fields;
        }
    }

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        self::$definition = [
                'table' => 'ebay_log',
                'primary' => 'id_ebay_log',
                'fields' => [
                    'text' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                    'type' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
                ],
            ];

        return parent::__construct($id, $id_lang, $id_shop);
    }

    public static function write($text, $type)
    {
        $ebay_log = new EbayLog();
        $ebay_log->text = $text;
        $ebay_log->type = $type;

        return $ebay_log->save();
    }
}
