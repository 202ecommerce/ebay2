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
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('TMP_DS')) {
    define('TMP_DS', DIRECTORY_SEPARATOR);
}

require_once dirname(__FILE__) . TMP_DS . '..' . TMP_DS . '..' . TMP_DS . '..' . TMP_DS . 'config' . TMP_DS . 'config.inc.php';
include dirname(__FILE__) . '/../classes/EbayConfiguration.php';
if (!Tools::getValue('token') || Tools::getValue('token') != Configuration::get('EBAY_SECURITY_TOKEN')) {
    die('ERROR: Invalid Token');
}
$id_ebay_profile = Tools::getValue('profile');
$ebay_profile = new EbayProfile($id_ebay_profile);
$ebay_category = Tools::getValue('real_category_ebay');
// Insert and update categories
if ($ebay_category) {
    $percent = array();
    $ps_categories = explode(",", Tools::getValue('ps_categories'));

    $impact_prix = Tools::getValue('impact_prix');
    $percent_sign_type = $impact_prix['sign'];
    $percent['value'] = $impact_prix['value'];
    $percent['type'] = $impact_prix['type'];
    foreach ($ps_categories as $id_category) {
        $data = array();
        $date = date('Y-m-d H:i:s');
        if ($percent['value'] != '') {
            //$percent_sign_type = explode(':', $percent['sign']);
            $percentValue = ($percent_sign_type == '-' ? $percent_sign_type : '+') . round($percent['value'], 2) . ($percent['type'] == 'percent' ? '%' : '');
        } else {
            $percentValue = null;
        }

        $data = array(
            'id_ebay_profile' => (int)$id_ebay_profile,
            'id_country' => (int)$ebay_profile->ebay_site_id,
            'id_ebay_category' => (int)$ebay_category,
            'id_category' => (int)$id_category,
            'percent' => pSQL($percentValue),
            'date_upd' => pSQL($date),
            'sync' => 1,
        );
        if (EbayCategoryConfiguration::getIdByCategoryId($id_ebay_profile, $id_category)) {
            if ($data) {
                EbayCategoryConfiguration::updateByIdProfileAndIdCategory($id_ebay_profile, $id_category, $data);
            } else {
                EbayCategoryConfiguration::deleteByIdCategory($id_ebay_profile, $id_category);
            }
        } elseif ($data) {
            $data['date_add'] = $date;
            EbayCategoryConfiguration::add($data);
        }
    }
    // make sur the ItemSpecifics and Condition data are refresh when we load the dedicated config screen the next time
    $ebay_profile->deleteConfigurationByName('EBAY_SPECIFICS_LAST_UPDATE');
}

// update extra_images for all products
if (($all_nb_extra_images = Tools::getValue('all-extra-images-value', -1)) != -1) {
    $product_ids = EbayCategoryConfiguration::getAllProductIds($ebay_profile->id);

    foreach ($product_ids as $product_id) {
        EbayProductConfiguration::insertOrUpdate($product_id, array(
            'extra_images' => $all_nb_extra_images ? $all_nb_extra_images : 0,
            'id_ebay_profile' => $ebay_profile->id,
        ));
    }
}

// update products configuration
if (is_array(Tools::getValue('showed_products'))) {
    $showed_product_ids = array_keys(Tools::getValue('showed_products'));

    if (Tools::getValue('to_synchronize')) {
        $to_synchronize_product_ids = array_keys(Tools::getValue('to_synchronize'));
    } else {
        $to_synchronize_product_ids = array();
    }

    foreach ($showed_product_ids as $product_id) {
        EbayProductConfiguration::insertOrUpdate($product_id, array(
            'id_ebay_profile' => $id_ebay_profile,
            'blacklisted' => in_array($product_id, $to_synchronize_product_ids) ? 0 : 1,
            'extra_images' => 0,
        ));
    }

    foreach ($to_synchronize_product_ids as $product_id_to_sync) {
        $product = new Product($product_id_to_sync);
        EbayTaskManager::addTask('add', $product, Tools::getValue('id_employee'), $id_ebay_profile);
    }
    // TODO remove extra_image
}

if (Tools::getValue('payement_policies') || Tools::getValue('return_policies')) {
    $payment = Tools::getValue('payement_policies');
    $return = Tools::getValue('return_policies');
    $ebay_category_id_ref = EbayCategory::getIdCategoryRefById($ebay_category, $ebay_profile->ebay_site_id);
    $data = array(
        'id_ebay_profile' => (int)$id_ebay_profile,
        'id_category' => (int)$ebay_category_id_ref,
        'id_return' => pSQl($return),
        'id_payment' => pSQL($payment),
    );

    EbayBussinesPolicies::deletePoliciesConfgbyidCategories($id_ebay_profile, $ebay_category_id_ref);

    Db::getInstance()->insert('ebay_category_business_config', $data);

    $ebay_profile->setConfiguration('EBAY_BUSINESS_POLICIES_CONFIG', 1);
}

if (Tools::getValue('specific')) {
    foreach (Tools::getValue('specific') as $specific_id => $data) {
        if ($data) {
            list($data_type, $value) = explode('-', $data);
        } else {
            $data_type = null;
        }

        $field_names = EbayCategorySpecific::getPrefixToFieldNames();
        $data = array_combine(array_values($field_names), array(null, null, null, null, null, null, null));

        if ($data_type) {
            $data[$field_names[$data_type]] = pSQL($value);
        }

        Db::getInstance()->update('ebay_category_specific', $data, 'id_ebay_category_specific = ' . (int)$specific_id);
    }
}

// save conditions
if (Tools::getValue('condition')) {
    foreach (Tools::getValue('condition') as $category_id => $condition) {
        $ebay_category_id_ref = EbayCategory::getIdCategoryRefById($category_id, $ebay_profile->ebay_site_id);
        foreach ($condition as $type => $condition_ref) {
            EbayCategoryConditionConfiguration::replace(array('id_ebay_profile' => $id_ebay_profile, 'id_condition_ref' => $condition_ref, 'id_category_ref' => $ebay_category_id_ref, 'condition_type' => $type));
        }
    }
}

// Insert and update categories
if ($store_categories = Tools::getValue('store_category')) {
    // insert rows
    EbayStoreCategoryConfiguration::update($id_ebay_profile, $store_categories, Tools::getValue('ps_categories'));
}


if (Tools::getValue('ajax')) {
    die('{"valid" : true}');
}
