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

if (!defined('TMP_DS')) {
    define('TMP_DS', DIRECTORY_SEPARATOR);
}

require_once dirname(__FILE__).TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'config'.TMP_DS.'config.inc.php';
include dirname(__FILE__).'/../classes/EbayConfiguration.php';
if (!Tools::getValue('token') || Tools::getValue('token') != Configuration::get('EBAY_SECURITY_TOKEN')) {
    die('ERROR: Invalid Token');
}

// Insert and update categories
if ($ebay_categories = Tools::getValue('category')) {

    $id_ebay_profile = Tools::getValue('profile');

    $ps_categories = explode(",",Tools::getValue('ps_categories'));

    $percent_sign_type[0] = '+';
    $percent['value'] = '';
    $percent['type'] = 'percent';
    foreach ($ps_categories as $id_category ) {
        $data = array();
        $date = date('Y-m-d H:i:s');
        if ($percent['value'] != '') {
            //$percent_sign_type = explode(':', $percent['sign']);
            $percentValue = ($percent_sign_type[0] == '-' ? $percent_sign_type[0] : '').Tools::getValue('impact_prix').($percent['type'] == 'percent' ? '%' : '');
        } else {
            $percentValue = null;
        }

        $data = array(
            'id_ebay_profile' => (int) $id_ebay_profile,
            'id_country' => 8,
            'id_ebay_category' => (int) $ebay_categories[$id_category],
            'id_category' => (int) $id_category,
            'percent' => pSQL($percentValue),
            'date_upd' => pSQL($date),
            'sync' => 0,
        );
        var_dump($data);die;
       /* if (EbayCategoryConfiguration::getIdByCategoryId($id_ebay_profile, $id_category)) {
            if ($data) {
                EbayCategoryConfiguration::updateByIdProfileAndIdCategory($id_ebay_profile, $id_category, $data);
            } else {
                EbayCategoryConfiguration::deleteByIdCategory($id_ebay_profile, $id_category);
            }

        } elseif ($data) {
            $data['date_add'] = $date;
            EbayCategoryConfiguration::add($data);
        }*/
    }

    // make sur the ItemSpecifics and Condition data are refresh when we load the dedicated config screen the next time
    $this->ebay_profile->deleteConfigurationByName('EBAY_SPECIFICS_LAST_UPDATE');
}

// update extra_images for all products
if (($all_nb_extra_images = Tools::getValue('all-extra-images-value', -1)) != -1) {
    $product_ids = EbayCategoryConfiguration::getAllProductIds($this->ebay_profile->id);

    foreach ($product_ids as $product_id) {
        EbayProductConfiguration::insertOrUpdate($product_id, array(
            'extra_images' => $all_nb_extra_images ? $all_nb_extra_images : 0,
            'id_ebay_profile' => $this->ebay_profile->id,
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

    // TODO remove extra_images


    foreach ($showed_product_ids as $product_id) {
        EbayProductConfiguration::insertOrUpdate($product_id, array(
            'id_ebay_profile' => $id_ebay_profile,
            'blacklisted' => in_array($product_id, $to_synchronize_product_ids) ? 0 : 1,
            'extra_images' => 0,
        ));
    }

}

if (Tools::getValue('ajax')) {
    die('{"valid" : true}');
}
