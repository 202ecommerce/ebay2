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

/**
 * @param Ebay $module
 *
 * @return bool
 *
 * @throws PrestaShopDatabaseException
 */
function upgrade_module_1_8($module)
{
    $sql = [];
    include dirname(__FILE__) . '/sql/sql-upgrade-1-8.php';

    if (!empty($sql) && is_array($sql)) {
        foreach ($sql as $request) {
            if (!Db::getInstance()->execute($request)) {
                return false;
            }
        }
    }

    // upgrade existing profiles
    $profiles = EbayProfile::getAllProfile();
    foreach ($profiles as $profile) {
        $ebay_profile = new EbayProfile($profile['id_ebay_profile']);

        // set id_lang if not set
        if (!$profile['id_lang']) {
            $ebay_profile->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
            $ebay_profile->save();
        }

        if ($ebay_profile->ebay_site_id) {
            $ebay_shop_country = EbayCountrySpec::getIsoCodeBySiteId($ebay_profile->ebay_site_id);
            $ebay_site_id = $ebay_profile->ebay_site_id;
        } else {
            if ($ebay_profile->getConfiguration('EBAY_COUNTRY_DEFAULT')) {
                $ebay_shop_country = $ebay_profile->getConfiguration('EBAY_COUNTRY_DEFAULT');
            } else {
                $ebay_shop_country = 'fr';
                $ebay_profile->setConfiguration('EBAY_COUNTRY_DEFAULT', $ebay_shop_country);
            }
            $ebay_profile->ebay_site_id = EbayCountrySpec::getSiteIdByIsoCode($ebay_shop_country);
            $ebay_profile->save();
            $ebay_site_id = $ebay_profile->ebay_site_id;
        }

        // we set EBAY_SHOP_COUNTRY
        $ebay_profile->setConfiguration('EBAY_SHOP_COUNTRY', $ebay_shop_country);
        $dbEbay = new DbEbay();
        $dbEbay->setDb(Db::getInstance());
        // update ebay_category
        $dbEbay->autoExecute(_DB_PREFIX_ . 'ebay_category', ['id_country' => $ebay_site_id], 'UPDATE');

        if ($ebay_profile->getCatalogConfiguration('EBAY_CATEGORY_LOADED')) {
            $ebay_profile->setCatalogConfiguration('EBAY_CATEGORY_LOADED', 0);
        }

        // update ebay_category_specific
        $dbEbay->autoExecute(_DB_PREFIX_ . 'ebay_category_specific', ['ebay_site_id' => $ebay_site_id], 'UPDATE');

        // update ebay_product_configuration
        $ebay_profile = EbayProfile::getCurrent();
        $dbEbay->autoExecute(_DB_PREFIX_ . 'ebay_product_configuration', ['id_ebay_profile' => $ebay_profile->id], 'UPDATE');
    }

    return true;
}
