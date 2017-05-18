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

class EbayReturnsPolicyConfiguration extends ObjectModel
{
    const DEFAULT_RETURNS_WITHIN = 'Days_14';
    const DEFAULT_RETURNS_WHO_PAYS = 'Seller';
    const DEFAULT_RETURNS_DESCRIPTION = '';
    const DEFAULT_RETURNS_ACCEPTED_OPTION = 'ReturnsAccepted';

    public $id_ebay_returns_policy_configuration;
    public $ebay_returns_within;
    public $ebay_returns_who_pays;
    public $ebay_returns_description;
    public $ebay_returns_accepted_option;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition;

    // for Prestashop 1.4
    protected $tables;
    protected $fieldsRequired = array();
    protected $fieldsSize = array();
    protected $fieldsValidate = array();
    protected $table = 'ebay_returns_policy_configuration';
    protected $identifier = 'id_ebay_returns_policy_configuration';

    public function getFields()
    {
        
        $fields = array();
        parent::validateFields();
        if (isset($this->id)) {
            $fields['id_ebay_returns_policy_configuration'] = (int) ($this->id);
        }

        $fields['ebay_returns_within'] = pSQL($this->ebay_returns_within);
        $fields['ebay_returns_who_pays'] = pSQL($this->ebay_returns_who_pays);
        $fields['ebay_returns_description'] = pSQL($this->ebay_returns_description, true);
        $fields['ebay_returns_accepted_option'] = pSQL($this->ebay_returns_accepted_option);

        return $fields;
    }

    public function __construct($id_ebay_returns_policy_configuration = null, $id_lang = null, $id_shop = null)
    {

            self::$definition = array(
                'table' => 'ebay_returns_policy_configuration',
                'primary' => 'id_ebay_returns_policy_configuration',
                'fields' => array(
                    'ebay_returns_within' => array('type' => self::TYPE_STRING, 'size' => 255, 'default' => self::DEFAULT_RETURNS_WITHIN),
                    'ebay_returns_who_pays' => array('type' => self::TYPE_STRING, 'size' => 255, 'default' => self::DEFAULT_RETURNS_WHO_PAYS),
                    'ebay_returns_description' => array('type' => self::TYPE_STRING, 'default' => self::DEFAULT_RETURNS_DESCRIPTION),
                    'ebay_returns_accepted_option' => array('type' => self::TYPE_STRING, 'size' => 255, 'default' => self::DEFAULT_RETURNS_ACCEPTED_OPTION),
                ),
            );

        return parent::__construct($id_ebay_returns_policy_configuration, $id_lang, $id_shop);
    }

    public static function getDefaultObjectId()
    {
        $sql = 'SELECT `id_ebay_returns_policy_configuration`
			FROM `'._DB_PREFIX_.'ebay_returns_policy_configuration` erpc
			WHERE erpc.`ebay_returns_within`= \''.pSQL(self::DEFAULT_RETURNS_WITHIN).'\'
			AND erpc.`ebay_returns_who_pays` = \''.pSQL(self::DEFAULT_RETURNS_WHO_PAYS).'\'
			AND erpc.`ebay_returns_description` = \''.pSQL(self::DEFAULT_RETURNS_DESCRIPTION).'\'
			AND erpc.`ebay_returns_accepted_option` = \''.pSQL(self::DEFAULT_RETURNS_ACCEPTED_OPTION).'\'';
        if ($row = Db::getInstance()->getRow($sql)) {
            return $row['id_ebay_returns_policy_configuration'];
        }
    }

    // for upgrade to eBay module version 1.7
    public static function createPreviousDefaultConfiguration()
    {
        $returns_policy_configuration = new EbayReturnsPolicyConfiguration();
        $returns_policy_configuration->ebay_returns_within = Configuration::get('EBAY_RETURNS_WITHIN');
        $returns_policy_configuration->ebay_returns_who_pays = Configuration::get('EBAY_RETURNS_WHO_PAYS');
        $returns_policy_configuration->ebay_returns_description = Configuration::get('EBAY_RETURNS_DESCRIPTION');
        $returns_policy_configuration->ebay_returns_accepted_option = Configuration::get('EBAY_RETURNS_ACCEPTED_OPTION');
        $returns_policy_configuration->save();
        return $returns_policy_configuration->id;
    }
}
