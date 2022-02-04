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
 *
 */

use Ebay\classes\SDK\Lib\AspectConstraint;
use Ebay\classes\SDK\Lib\AspectValue;
use Ebay\classes\SDK\Taxonomy\GetItemAspectsForCategory\GetItemAspectsForCategory;

require_once dirname(__FILE__).'/EbayRequest.php';
require_once dirname(__FILE__).'/EbayCategorySpecificValue.php';

class EbayCategorySpecific
{
    const SELECTION_MODE_FREE_TEXT = 0;
    const SELECTION_MODE_SELECTION_ONLY = 1;

    private static $prefix_to_field_names = array(
        'attr'      => 'id_attribute_group',
        'feat'      => 'id_feature',
        'spec'      => 'id_ebay_category_specific_value',
        'brand'     => 'is_brand', // item specifics brand
        'reference' => 'is_reference',
        'ean'       => 'is_ean',
        'upc'       => 'is_upc',
    );

    /**
     * Returns an array containing the correspondance between the form select fields prefix and the table field name
     * @return array
     */
    public static function getPrefixToFieldNames()
    {
        return EbayCategorySpecific::$prefix_to_field_names;
    }

    public static function initGetAspectsCommand(EbayProfile $ebayProfile, $ebay_category_id)
    {
        return new GetItemAspectsForCategory($ebayProfile, $ebay_category_id);
    }

    /**
     * Parse the data returned by the API and enter them in the table
     *
     * @param int      $id_ebay_profile
     * @param bool|int $id_category
     * @return bool
     */
    public static function loadCategorySpecifics($id_ebay_profile, $id_category = false)
    {
        $request = new EbayRequest($id_ebay_profile);

        if ($id_category) {
            $ebay_category_ids = array($id_category);
        } else {
            $ebay_category_ids = EbayCategoryConfiguration::getEbayCategoryIds($id_ebay_profile);
        }

        $ebay_profile = new EbayProfile($id_ebay_profile);

        foreach ($ebay_category_ids as $ebay_category_id) {
            $getAspectsCommande = self::initGetAspectsCommand($ebay_profile, $ebay_category_id);
            $response = $getAspectsCommande->execute();

            if ($response->isSuccess() == false) {
                continue;
            }

            foreach ($response->getAspectList()->getList() as $aspect) {
                $required = $aspect->getAspectConstraint()->isAspectRequired();
                $can_variation = $aspect->getAspectConstraint()->isAspectEnabledForVariations();

                if (is_null($aspect->getAspectConstraint()->getAspectMaxLength())) {
                    $max_values = 1;
                } else {
                    $max_values = $aspect->getAspectConstraint()->getAspectMaxLength();
                }

                if ($aspect->getAspectConstraint()->getAspectMode() == AspectConstraint::MODE_SELECT_ONLY) {
                    $selection_mode = EbayCategorySpecific::SELECTION_MODE_SELECTION_ONLY;
                } else {
                    $selection_mode = EbayCategorySpecific::SELECTION_MODE_FREE_TEXT;
                }

                $values = array_map(
                    function (AspectValue $aspectValue) {
                        return $aspectValue->getLocalizedValue();
                    },
                    $aspect->getAspectValues()->getList()
                );

                $db = Db::getInstance();

                $sql = 'INSERT INTO `'._DB_PREFIX_.'ebay_category_specific` (`id_category_ref`, `name`, `required`, `can_variation`, `selection_mode`, `ebay_site_id`, `max_values`)
						VALUES ('.(int)$ebay_category_id.', \''.pSQL((string)$aspect->getLocalizedAspectName()).'\', '.($required ? 1 : 0).', '.($can_variation ? 1 : 0).', '.($selection_mode ? 1 : 0).', '.(int)$ebay_profile->ebay_site_id.', ' . $max_values . ')
						ON DUPLICATE KEY UPDATE `required` = '.($required ? 1 : 0).', `can_variation` = '.($can_variation ? 1 : 0).', `selection_mode` = '.($selection_mode ? 1 : 0) . ', `max_values` = ' . $max_values . ', ebay_site_id=' . (int)$ebay_profile->ebay_site_id;

                $db->execute($sql);

                $ebay_category_specific_id = $db->Insert_ID();

                if (!$ebay_category_specific_id) {
                    $ebay_category_specific_id = $db->getValue('SELECT `id_ebay_category_specific`
							FROM `'._DB_PREFIX_.'ebay_category_specific`
							WHERE `id_category_ref` = '.(int)$ebay_category_id.'
							AND `ebay_site_id` = '.(int)$ebay_profile->ebay_site_id.'
							AND `name` = \''.pSQL((string)$aspect->getLocalizedAspectName()).'\'');
                }

                $insert_data = array();

                foreach ($values as $value) {
                    $insert_data[] = array(
                        'id_ebay_category_specific' => (int)$ebay_category_specific_id,
                        'value'                     => pSQL($value),
                    );
                }

                EbayCategorySpecificValue::insertIgnore($insert_data);
            }
        }

        return true;
    }

    public static function atLeastOneOptionalSpecificIsConfigured($id_ebay_profile)
    {
        $optional_items = EbayCategorySpecific::getAllOptional($id_ebay_profile);
        foreach ($optional_items as $item) {
            if (EbayCategorySpecific::isConfigured($item)) {
                return true;
            }
        }

        return false;
    }

    public static function allMandatorySpecificsAreConfigured($id_ebay_profile)
    {
        $mandatory_items = EbayCategorySpecific::getAllMandatory($id_ebay_profile);
        foreach ($mandatory_items as $item) {
            if (!EbayCategorySpecific::isConfigured($item)) {
                return false;
            }
        }
        return true;
    }

    public static function isConfigured($item_specific)
    {
        if ($item_specific['id_attribute_group'] != 0 || $item_specific['id_feature'] != 0 || $item_specific['id_ebay_category_specific_value'] != 0 || $item_specific['is_brand'] != 0 || $item_specific['is_reference'] != 0 || $item_specific['is_ean'] != 0 || $item_specific['is_upc'] != 0) {
            return true;
        }

        return false;
    }

    public static function getAllMandatory($id_ebay_profile)
    {
        $ebay_profile = new EbayProfile($id_ebay_profile);

        return Db::getInstance()->ExecuteS('
			SELECT * FROM '._DB_PREFIX_.'ebay_category_specific ecs
			INNER JOIN '._DB_PREFIX_.'ebay_category ec
			ON ecs.id_category_ref = ec.id_category_ref
			AND ecs.`ebay_site_id` = ec.`id_country`
			AND ec.`id_country` = '.(int)$ebay_profile->ebay_site_id.'
			INNER JOIN `'._DB_PREFIX_.'ebay_category_configuration` ecc
			ON ec.`id_ebay_category` = ecc.`id_ebay_category`
			AND ecc.`id_ebay_profile` = '.(int)$id_ebay_profile.'
			WHERE required = 1');
    }

    public static function getAllOptional($id_ebay_profile)
    {
        $ebay_profile = new EbayProfile($id_ebay_profile);

        return Db::getInstance()->ExecuteS('
			SELECT * FROM '._DB_PREFIX_.'ebay_category_specific ecs
			INNER JOIN '._DB_PREFIX_.'ebay_category ec
			ON ecs.id_category_ref = ec.id_category_ref
			AND ecs.`ebay_site_id` = ec.`id_country`
			AND ec.`id_country` = '.(int)$ebay_profile->ebay_site_id.'
			INNER JOIN `'._DB_PREFIX_.'ebay_category_configuration` ecc
			ON ec.`id_ebay_category` = ecc.`id_ebay_category`
			AND ecc.`id_ebay_profile` = '.(int)$id_ebay_profile.'
			WHERE required = 0');
    }

    public static function getNbOptionalItemSpecifics($id_ebay_profile)
    {
        $ebay_profile = new EbayProfile($id_ebay_profile);
        $sql = 'SELECT count(*)
			FROM `'._DB_PREFIX_.'ebay_category_specific` ecs
			INNER JOIN `'._DB_PREFIX_.'ebay_category` ec
			ON ecs.`id_category_ref` = ec.`id_category_ref`
			AND ecs.`ebay_site_id` = ec.`id_country`
			AND ec.`id_country` = '.(int)$ebay_profile->ebay_site_id.'
			INNER JOIN `'._DB_PREFIX_.'ebay_category_configuration` ecc
			ON ec.`id_ebay_category` = ecc.`id_ebay_category`
			AND ecc.`id_ebay_profile` = '.(int)$id_ebay_profile.'
			WHERE ecs.`required` = 0
			AND `id_ebay_category_specific_value` > 0';

        return Db::getInstance()->getValue($sql);
    }
}
