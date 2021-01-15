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

class EbayCategory
{
    private $id_category; /* PrestaShop category id */
    private $id_category_ref; /* eBay Category id */
    private $id_country; /* eBay Site id. naming is not great */
    private $is_multi_sku;
    private $k_type;
    private $ebay_profile;
    private $id; /*id from table ebay_category*/

    private $percent;

    private $items_specific_values;
    private $conditions_values;
    private $best_offer_enabled;

    public function __construct($ebay_profile, $id_category_ref, $id_category = null)
    {
        if ($ebay_profile) {
            $this->ebay_profile = $ebay_profile;
            $this->id_country = (int)$ebay_profile->ebay_site_id;
        }
        if ($id_category_ref) {
            $this->id_category_ref = (int)$id_category_ref;
        }

        if ($id_category) {
            $this->id_category = (int)$id_category;
        }

        $this->_loadFromDb();
    }

    private function _loadFromDb()
    {
        $sql = 'SELECT ecc.`id_category`, ec.`id_category_ref`, ec.`id_ebay_category` as `id`, ec.`is_multi_sku`, ecc.`percent` FROM `'._DB_PREFIX_.'ebay_category` ec
			LEFT JOIN `'._DB_PREFIX_.'ebay_category_configuration` ecc
			ON (ecc.`id_ebay_category` = ec.`id_ebay_category`)
			AND ecc.`id_ebay_profile` = '.(int)$this->ebay_profile->id.'
			WHERE ec.`id_country` = '.(int)$this->id_country.' AND ';

        if ($this->id_category_ref) {
            $sql .= 'ec.`id_category_ref` = '.(int)$this->id_category_ref;
        } else {
            $sql .= 'ecc.`id_category` = '.(int)$this->id_category;
        }

        $res = Db::getInstance()->getRow($sql);
        if ($res) {
            foreach ($res as $attribute => $value) {
                $this->$attribute = $value;
            }
        }
    }

    public function getIdCategoryRef()
    {
        if ($this->id_category_ref === null) {
            $this->_loadFromDb();
        }

        return $this->id_category_ref;
    }

    public function isMultiSku()
    {
        if ($this->is_multi_sku === null) {
            $this->_loadFromDb();
        }

        if ($this->is_multi_sku === null) {
            $this->is_multi_sku = EbayCategory::getInheritedIsMultiSku((int)$this->id_category_ref, $this->id_country);
        }

        return $this->is_multi_sku;
    }

    public function isKtype()
    {

            $this->k_type = EbayCategory::getKtype((int)$this->id_category_ref, $this->id_country);


        return $this->k_type;
    }

    public function getPercent()
    {
        if ($this->percent === null) {
            $this->_loadFromDb();
        }

        return $this->percent;
    }

    /**
     * returns the percent, without the percent sign if there is one
     */
    public function getCleanPercent()
    {
        return preg_replace('#%$#is', '', $this->percent);
    }

    /**
     * Returns the items specifics with the PrestaShop attribute group / feature / of eBay specifics matching
     */
    public function getItemsSpecifics()
    {
        $sql = 'SELECT e.`name`, e.`id_ebay_category_specific` as id, e.`required`, e.`selection_mode`, 
                e.`id_attribute_group`, e.`id_feature`, e.`id_ebay_category_specific_value` as id_specific_value, 
                e.`is_brand`, e.`can_variation`, e.`is_reference`, e.`is_ean`, e.`is_upc`, e.`max_values`
			FROM `'._DB_PREFIX_.'ebay_category_specific` e
			WHERE e.`id_category_ref` = '.(int)$this->id_category_ref.'
			AND e.`ebay_site_id` = '.(int)$this->id_country;

        return DB::getInstance()->executeS($sql);
    }

    /**
     * Returns the items specifics with the full value for ebay_specific_attributes
     */
    public function getItemsSpecificValues()
    {
        if (!$this->items_specific_values) {
            if (!$this->id_category_ref) {
                $this->_loadFromDb();
            }

            $sql = 'SELECT e.`name`, e.`can_variation`, e.`id_attribute_group`, e.`id_feature`, e.`max_values`,
                ec.`value` AS specific_value, e.`is_brand`, e.`is_reference`, e.`is_ean`, e.`is_upc`
				FROM `'._DB_PREFIX_.'ebay_category_specific` e
				LEFT JOIN `'._DB_PREFIX_.'ebay_category_specific_value` ec
				ON e.`id_ebay_category_specific_value` = ec.`id_ebay_category_specific_value`
				WHERE e.`id_category_ref` = '.(int)$this->id_category_ref.'
				AND e.`ebay_site_id` = '.(int)$this->id_country;

            $this->items_specific_values = Db::getInstance()->executeS($sql);
        }
       
        return $this->items_specific_values;
    }

    /**
     * Returns the category conditions with the corresponding types on PrestaShop if set
     * @param int $id_ebay_profile
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getConditionsWithConfiguration($id_ebay_profile)
    {
        $sql = 'SELECT e.`id_condition_ref` AS id, e.`name`, ec.`condition_type` as type
			FROM `'._DB_PREFIX_.'ebay_category_condition` e
			LEFT JOIN `'._DB_PREFIX_.'ebay_category_condition_configuration` ec
			ON e.`id_category_ref` = ec.`id_category_ref`
			AND e.`id_condition_ref` = ec.`id_condition_ref`
			AND e.`id_ebay_profile` = ec.`id_ebay_profile`
			WHERE e.`id_category_ref` = '.(int)$this->id_category_ref.'
			AND e.`id_ebay_profile` = '.(int)$id_ebay_profile;

        $res = Db::getInstance()->executeS($sql);

        $ret = array();

        foreach ($res as $row) {
            if (!isset($ret[$row['id']])) {
                $ret[$row['id']] = array(
                    'name'  => $row['name'],
                    'types' => array($row['type']),
                );
            } else {
                $ret[$row['id']]['types'][] = $row['type'];
            }
        }

        return $ret;
    }

    /**
     * Returns an array with the condition_type and corresponding ConditionID on eBay
     * @param int $id_ebay_profile
     * @return
     * @throws PrestaShopDatabaseException
     */
    public function getConditionsValues($id_ebay_profile)
    {
        if (!isset($this->conditions_values[$id_ebay_profile])) {
            $sql = 'SELECT e.condition_type, e.id_condition_ref as condition_id
				FROM '._DB_PREFIX_.'ebay_category_condition_configuration e
				WHERE e.`id_ebay_profile` = '.(int)$id_ebay_profile.'
				AND e.id_category_ref = '.(int)$this->id_category_ref;

            $res = Db::getInstance()->executeS($sql);

            $ret = array(
                'new'         => null,
                'used'        => null,
                'refurbished' => null,
            );

            foreach ($res as $row) {
                $ret[EbayCategoryConditionConfiguration::getPSConditions($row['condition_type'])] = $row['condition_id'];
            }

            $this->conditions_values[$id_ebay_profile] = $ret;
        }

        return $this->conditions_values[$id_ebay_profile];
    }

    public static function deleteCategoriesByIdCountry($id_country)
    {
        return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'ebay_category` WHERE `id_country` = '.(int)$id_country);
    }

    public static function insertCategories($ebay_site_id, $categories, $categories_multi_sku)
    {
        $insert_data = array();
        foreach ($categories as $category) {
            $one = '(' . pSQL($category['CategoryID']);
            $one .= ',' . pSQL($category['CategoryParentID']);
            $one .= ',' . pSQL($ebay_site_id);
            $one .= ',' . pSQL($category['CategoryLevel']);
            if (array_key_exists($category['CategoryID'], $categories_multi_sku)) {
                $one .= ',' . $categories_multi_sku[$category['CategoryID']];
            } else {
                $one .= ',NULL';
            }
            $one .= ', \'' . pSQL($category['CategoryName']) . '\')';

            $insert_data[] = $one;
        }

        $insert = 'INSERT INTO ' . _DB_PREFIX_ . 'ebay_category (id_category_ref, 
                    id_category_ref_parent, id_country, level, is_multi_sku, name) VALUES ' . implode(', ', $insert_data);
        $result = DB::getInstance()->Execute($insert);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public static function updateCategoryTable($categories_multi_sku)
    {
        $db = Db::getInstance();

        $dbEbay = new DbEbay();
        $dbEbay->setDb($db);

        $categories = $db->ExecuteS('SELECT * FROM '._DB_PREFIX_.'ebay_category');

        foreach ($categories as $category) {
            $dbEbay->autoExecute(_DB_PREFIX_.'ebay_category', array(
                'is_multi_sku' => isset($categories_multi_sku[$category['id_category_ref']]) ? (int)$categories_multi_sku[$category['id_category_ref']] : null,
            ), 'UPDATE', '`id_category_ref` = '.(int)$category['id_category_ref'], 0, true, true);
        }

        Configuration::updateValue('EBAY_CATEGORY_MULTI_SKU_UPDATE', 1, false, 0, 0);
    }

    /**
     * Climbs up the categories hierarchy until finding the value inherited for is_multi_sku
     * @param int $id_category_ref
     * @param int $ebay_site_id
     * @return
     */
    public static function getInheritedIsMultiSku($id_category_ref, $ebay_site_id)
    {
        $row = Db::getInstance()->getRow('SELECT `id_category_ref_parent`, `is_multi_sku`
			FROM `'._DB_PREFIX_.'ebay_category`
			WHERE `id_category_ref` = '.(int)$id_category_ref.'
			AND `id_country` = '.(int)$ebay_site_id);

        if ($row['is_multi_sku'] !== null) {
            return $row['is_multi_sku'];
        }

        if ((int)$row['id_category_ref_parent'] != (int)$id_category_ref) {
            return EbayCategory::getInheritedIsMultiSku($row['id_category_ref_parent'], $ebay_site_id);
        }

        return $row['is_multi_sku'];
    }

    public static function getKtype($id_category_ref, $ebay_site_id)
    {
        $row = Db::getInstance()->getRow('SELECT `k_type`
			FROM `'._DB_PREFIX_.'ebay_category`
			WHERE `id_category_ref` = '.(int)$id_category_ref.'
			AND `id_country` = '.(int)$ebay_site_id);

        return $row['k_type'];
    }

    public static function getIdCategoryRefById($id_category, $ebay_site_id)
    {
        $row = Db::getInstance()->getRow('SELECT `id_category_ref`
			FROM `'._DB_PREFIX_.'ebay_category`
			WHERE `id_ebay_category` = '.(int)$id_category.'
			AND `id_country` = '.(int)$ebay_site_id);

        return $row['id_category_ref'];
    }


    public static function areCategoryLoaded($ebay_site_id)
    {
        if (Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'ebay_category ec WHERE ec.`id_country` = '.(int)$ebay_site_id) == 0) {
            return false;
        }

        return true;
    }

    public static function setKtypeConfiguration($id_category_ref, $value, $id_ebay_profile)
    {
        $dbEbay = new DbEbay();
        $db = Db::getInstance();
        $dbEbay->setDb($db);
        $ebay_profile= new EbayProfile($id_ebay_profile);
        $ebay_site_id = $ebay_profile->ebay_site_id;
        $dbEbay->autoExecute(_DB_PREFIX_.'ebay_category', array(
            'k_type' => ($value == 'true') ? 1 : 0,
            ), 'UPDATE', '`id_category_ref` = '.(int)$id_category_ref .' AND `id_country` = ' . (int)$ebay_site_id, 0, true, true);
    }

    public static function setBestOffer($id_category_ref, $value, $id_ebay_profile)
    {
        $dbEbay = new DbEbay();
        $db = Db::getInstance();
        $dbEbay->setDb($db);
        $ebay_profile= new EbayProfile($id_ebay_profile);
        $ebay_site_id = $ebay_profile->ebay_site_id;
        $dbEbay->autoExecute(_DB_PREFIX_.'ebay_category', array(
            'best_offer_enabled' => ($value == 'true') ? 1 : 0,
        ), 'UPDATE', '`id_category_ref` = '.(int)$id_category_ref .' AND `id_country` = ' . (int)$ebay_site_id, 0, true, true);
    }

    public static function getBestOffer($id_category_ref, $ebay_site_id)
    {
        $row = Db::getInstance()->getRow('SELECT `best_offer_enabled`
			FROM `'._DB_PREFIX_.'ebay_category`
			WHERE `id_category_ref` = '.(int)$id_category_ref.'
			AND `id_country` = '.(int)$ebay_site_id);

        return $row['best_offer_enabled'];
    }
}
