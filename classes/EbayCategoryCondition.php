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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

if (file_exists(dirname(__FILE__).'/EbayRequest.php')) {
    require_once dirname(__FILE__).'/EbayRequest.php';
}

class EbayCategoryCondition
{
    /**
     * Parse the data returned by the API for the eBay Category Conditions
     * @param int  $id_ebay_profile
     * @param bool $id_category
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function loadCategoryConditions($id_ebay_profile, $id_category = false)
    {
        $request = new EbayRequest($id_ebay_profile);

        if ($id_category) {
            $ebay_category_ids = array($id_category);
        } else {
            $ebay_category_ids = EbayCategoryConfiguration::getEbayCategoryIds((int)$id_ebay_profile);
        }

        $conditions = array();

        foreach ($ebay_category_ids as $category_id) {
            $xml_data = $request->getCategoryFeatures($category_id);

            if (isset($xml_data->Category->KTypeSupported )) {
                $ktype_value = $xml_data->Category->KTypeSupported;

                EbayCategory::setKtypeConfiguration($category_id, $ktype_value, $id_ebay_profile);
                $ebay_profile = new EbayProfile($id_ebay_profile);
                if (EbayCategory::getKtype($category_id, $ebay_profile->ebay_site_id) == 1) {
                    $sql = 'INSERT INTO `'._DB_PREFIX_.'ebay_category_specific` (`id_category_ref`, `name`, `required`, `can_variation`, `selection_mode`, `ebay_site_id`)
						VALUES ('.(int)$category_id.', \'K-type\', 1, 0, 0, '.(int)$ebay_profile->ebay_site_id.')
						ON DUPLICATE KEY UPDATE `required` = 1, `can_variation` = 0, `selection_mode` = 0';
                    Db::getInstance()->execute($sql);
                }
            }

            if (isset($xml_data->Category->ConditionEnabled)) {
                $condition_enabled = $xml_data->Category->ConditionEnabled;
            } else {
                $condition_enabled = $xml_data->SiteDefaults->ConditionEnabled;
            }

            if (!$condition_enabled) {
                return;
            }

            if (isset($xml_data->Category->ConditionValues->Condition)) {
                $xml_conditions = $xml_data->Category->ConditionValues->Condition;
            } else {
                $xml_conditions = $xml_data->SiteDefaults->ConditionValues->Condition;
            }

            if ($xml_conditions) {
                foreach ($xml_conditions as $xml_condition) {
                    $conditions[] = array(
                        'id_ebay_profile'  => (int)$id_ebay_profile,
                        'id_category_ref'  => (int)$category_id,
                        'id_condition_ref' => (int)$xml_condition->ID,
                        'name'             => pSQL((string)$xml_condition->DisplayName),
                    );
                }
            }

            //
            Db::getInstance()->ExecuteS("SELECT 1");
        }

        // security to make sure there are values to enter befor truncating the table
        if ($conditions) {
            $db = Db::getInstance();
            if (!$id_category) {
                $db->Execute('DELETE FROM '._DB_PREFIX_.'ebay_category_condition
				WHERE `id_ebay_profile` = '.(int)$id_ebay_profile);
            } else {
                $db->Execute('DELETE FROM '._DB_PREFIX_.'ebay_category_condition
				WHERE `id_ebay_profile` = '.(int)$id_ebay_profile.'
				AND `id_category_ref` = '.(int)$id_category);
            }


            $db->insert('ebay_category_condition', $conditions);


            return true;
        }

        return false;
    }
}
