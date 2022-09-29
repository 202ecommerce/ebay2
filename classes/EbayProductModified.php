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
class EbayProductModified extends ObjectModel
{
    public $id_product;
    public $id_ebay_profile;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition;

    // for Prestashop 1.4
    protected $tables;
    protected $fieldsRequired;
    protected $fieldsSize;
    protected $fieldsValidate;
    protected $table = 'ebay_product_modified';
    protected $identifier = 'id_ebay_product_modified';

    public function getFields()
    {
        $fields = [];
        parent::validateFields();
        if (isset($this->id)) {
            $fields['id_ebay_product_modified'] = (int) ($this->id);
        }

        $fields['id_ebay_profile'] = (int) ($this->id_ebay_profile);
        $fields['id_product'] = (int) ($this->id_product);

        return $fields;
    }

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        self::$definition = [
                'table' => 'ebay_product_modified',
                'primary' => 'id_ebay_product_modified',
                'fields' => [
                    'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
                    'id_ebay_profile' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
                ],
            ];

        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function addProduct($id_ebay_profile, $id_product)
    {
        $product_modified = new EbayProductModified();
        $product_modified->id_product = (int) $id_product;
        $product_modified->id_ebay_profile = (int) $id_ebay_profile;

        return $product_modified->save();
    }

    public static function getAll()
    {
        $sql = 'SELECT `id_ebay_profile`, `id_product`, `id_ebay_product_modified`
            FROM ' . _DB_PREFIX_ . 'ebay_product_modified GROUP BY id_product, id_ebay_profile';

        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    public static function truncate()
    {
        return Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'ebay_product_modified`');
    }
}
