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
 *  @copyright 2007-2020 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class EbayFormInterShippingTab extends EbayTab
{

    public function getContent()
    {
        $configKeys = array(
            'EBAY_SECURITY_TOKEN',
            'PS_LANG_DEFAULT',
        );
        // Load prestashop ebay's configuration
        $configs = Configuration::getMultiple($configKeys);

        $profile_configs = $this->ebay_profile->getMultiple(array(
            'EBAY_ZONE_INTERNATIONAL',
        ));

        // Check if the module is configured
        if (!$this->ebay_profile->getConfiguration('EBAY_ORDERS_CONFIG_TAB_OK')) {
            $vars = array(
                'msg' => $this->ebay->l('Please configure the \'Orders settings\' tab before using this tab', 'ebayformeconfigannoncestab'),
            );
            return $this->display('alert_tabs.tpl', $vars);
        }

        $nb_shipping_zones_excluded = DB::getInstance()->getValue('SELECT COUNT(*)
			FROM '._DB_PREFIX_.'ebay_shipping_zone_excluded
			WHERE `id_ebay_profile` = '.(int) $this->ebay_profile->id);

        if (!$nb_shipping_zones_excluded) {
            EbayShippingZoneExcluded::loadEbayExcludedLocations($this->ebay_profile->id);
        }

        $module_filters = Carrier::CARRIERS_MODULE;

        //INITIALIZE CACHE
        $psCarrierModule = $this->ebay_profile->getCarriers($configs['PS_LANG_DEFAULT'], false, false, false, null, $module_filters);

        $url_vars = array(
            'id_tab' => '103',
            'section' => 'intershipping',
        );

        $url_vars['controller'] = Tools::getValue('controller');

        $zones = Zone::getZones(true);
        foreach ($zones as &$zone) {
            $zone['carriers'] = Carrier::getCarriers($this->context->language->id, false, false, $zone['id_zone']);
            if ($zone['carriers']) {
                foreach ($zone['carriers'] as &$carrier) {
                    $sql = 'SELECT MIN(d.`price`) FROM `'._DB_PREFIX_.'delivery` d WHERE d.`id_zone` = '.(int)$zone['id_zone'].' AND d.`id_carrier` = '.(int)$carrier['id_carrier'];
                    $result = Db::getInstance()->getValue($sql);

                    if ($result == null) {
                        $carrier['price'] = 0;
                    } else {
                        $carrier['price'] = round($result, 2, PHP_ROUND_HALF_DOWN);
                    }
                }
            }
        }

        $template_vars = array(
            'eBayCarrier' => EbayShippingService::getCarriers($this->ebay_profile->ebay_site_id),
            'psCarrier' => $this->ebay_profile->getCarriers($configs['PS_LANG_DEFAULT']),
            'psCarrierModule' => $psCarrierModule,
            'existingInternationalCarrier' => EbayShippingInternationalZone::getExistingInternationalCarrier($this->ebay_profile->id),

            'prestashopZone' => Zone::getZones(),
            'excludeShippingLocation' => EbayShippingZoneExcluded::cacheEbayExcludedLocation($this->ebay_profile->id),
            'internationalShippingLocations' => EbayShippingLocation::getInternationalShippingLocations(),
            'formUrl' => $this->_getUrl($url_vars),
            'ebayZoneInternational' => (isset($profile_configs['EBAY_ZONE_INTERNATIONAL']) ? $profile_configs['EBAY_ZONE_INTERNATIONAL'] : false),
            'ebay_token' => $configs['EBAY_SECURITY_TOKEN'],
            'id_ebay_profile' => $this->ebay_profile->id,
            'newPrestashopZone' => $zones,
            'help_ship_cost' => array(
                'lang'           => $this->context->country->iso_code,
                'module_version' => $this->ebay->version,
                'ps_version'     => _PS_VERSION_,
                'error_code'     => 'HELP-SHIPPING-ADDITIONAL-ITEM-COST',
            ),

        );

        return $this->display('interShipping.tpl', $template_vars);
    }

    public function postProcess()
    {
        EbayShipping::truncateInternational($this->ebay_profile->id);
        //Update excluded location
        if (Tools::getValue('excludeLocationHidden')) {
            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'ebay_shipping_zone_excluded
				SET excluded = 0
				WHERE `id_ebay_profile` = '.(int) $this->ebay_profile->id);

            if ($exclude_locations = Tools::getValue('excludeLocation')) {
                $locations = array_keys($exclude_locations);
                $where = 'location IN ("'.implode('","', array_map('pSQL', $locations)).'")';

                $where .= ' AND `id_ebay_profile` = '.(int) $this->ebay_profile->id;

                DB::getInstance()->update('ebay_shipping_zone_excluded', array('excluded' => 1), $where);
            }
        }

        Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'ebay_shipping_international_zone
			WHERE `id_ebay_profile` = '.(int) $this->ebay_profile->id);

        if ($ebay_carriers_international = Tools::getValue('ebayCarrier_international')) {
            $ps_carriers_international = Tools::getValue('psCarrier_international');
            $extra_fees_international = Tools::getValue('extrafee_international');
            $international_shipping_locations = Tools::getValue('internationalShippingLocation');

            EbayShipping::truncateInternational($this->ebay_profile->id);
            foreach ($ebay_carriers_international as $key => $ebay_carrier_international) {
                if (!empty($ebay_carrier_international) && !empty($ps_carriers_international[$key]) && isset($international_shipping_locations[$key])) {
                    $infos = explode('-', $ps_carriers_international[$key]);

                    EbayShipping::insert($this->ebay_profile->id, $ebay_carrier_international, $infos[0], $extra_fees_international[$key], $infos[1], true);
                    $last_id = EbayShipping::getLastShippingId($this->ebay_profile->id);

                    foreach (array_keys($international_shipping_locations[$key]) as $id_ebay_zone) {
                        EbayShippingInternationalZone::insert($this->ebay_profile->id, $last_id, $id_ebay_zone);
                    }
                }
            }
        }
        $products = EbayProduct::getProductsWithoutBlacklisted($this->ebay_profile->id_lang, $this->ebay_profile->id, true);
        EbayTaskManager::deleteErrors($this->ebay_profile->id);
        while ($product_id = $products->fetch(PDO::FETCH_ASSOC)) {
            $product = new Product($product_id['id_product'], false, $this->ebay_profile->id_lang);
            EbayTaskManager::addTask('update', $product, null, $this->ebay_profile->id);
        }
        return $this->ebay->displayConfirmation($this->ebay->l('Settings updated'));
    }
}
