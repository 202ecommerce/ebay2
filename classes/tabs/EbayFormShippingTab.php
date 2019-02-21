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
 *  @copyright 2007-2019 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class EbayFormShippingTab extends EbayTab
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
            'EBAY_DELIVERY_TIME',
            'EBAY_ZONE_NATIONAL',
        ));

        if (!$this->ebay_profile->getConfiguration('EBAY_ORDERS_CONFIG_TAB_OK')) {
            $vars = array(
                'msg' => $this->ebay->l('Please configure the \'Orders settings\' tab before using this tab', 'ebayformeconfigannoncestab'),
            );
            return $this->display('alert_tabs.tpl', $vars);
        }
        // Check if the module is configured
        if (!$this->ebay_profile->getConfiguration('EBAY_PAYPAL_EMAIL')) {
            $template_vars = array('error_form_shipping' => 'true');
            return $this->display('error_paypal_email.tpl', $template_vars);
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
            'id_tab' => '3',
            'section' => 'shipping',
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
            'existingNationalCarrier' => EbayShipping::getNationalShippings($this->ebay_profile->id),
            'deliveryTime' => $profile_configs['EBAY_DELIVERY_TIME'],
            'prestashopZone' => Zone::getZones(),
            'deliveryTimeOptions' => EbayDeliveryTimeOptions::getDeliveryTimeOptions(),
            'formUrl' => $this->_getUrl($url_vars),
            'ebayZoneNational' => (isset($profile_configs['EBAY_ZONE_NATIONAL']) ? $profile_configs['EBAY_ZONE_NATIONAL'] : false),
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

        return $this->display('shipping.tpl', $template_vars);
    }

    public function postProcess()
    {

        //Update global information about shipping (delivery time, ...)
        $this->ebay_profile->setConfiguration('EBAY_DELIVERY_TIME', Tools::getValue('deliveryTime'));
        //Update Shipping Method for National Shipping (Delete And Insert)
        EbayShipping::truncateNational($this->ebay_profile->id);

        if ($ebay_carriers = Tools::getValue('ebayCarrier')) {
            $ps_carriers = Tools::getValue('psCarrier');
            $extra_fees = Tools::getValue('extrafee');
            $ps_ship = new Carrier($ps_carriers);
            EbayTaskManager::deleteErrors($this->ebay_profile->id);
            $ps_ship->getMaxDeliveryPriceByPrice($this->ebay_profile->id);
            foreach ($ebay_carriers as $key => $ebay_carrier) {
                if (!empty($ebay_carrier) && !empty($ps_carriers[$key])) {
                    //Get id_carrier and id_zone from ps_carrier
                    $infos = explode('-', $ps_carriers[$key]);
                    $ps_ship = new Carrier($infos[0]);
                    if ($ps_ship->shipping_method == 1) {
                        $ship_cost =$ps_ship->getMaxDeliveryPriceByWeight($infos[1]);
                    } else {
                        $ship_cost = $ps_ship->getMaxDeliveryPriceByPrice($infos[1]);
                    }
                    if ($ship_cost == false) {
                        $ship_cost = 0;
                    }
                    if (isset($extra_fees) && (int)$ship_cost < $extra_fees[$key]) {
                        return false;
                    }

                    EbayShipping::insert($this->ebay_profile->id, $ebay_carrier, $infos[0], $extra_fees[$key], $infos[1]);
                }
            }
            $products = EbayProduct::getProductsWithoutBlacklisted($this->ebay_profile->id_lang, $this->ebay_profile->id, true);
            $this->ebay_profile->setConfiguration('EBAY_SHIPPING_CONFIG_TAB_OK', 1);

            while ($product_id = $products->fetch(PDO::FETCH_ASSOC)) {
                $product = new Product($product_id['id_product'], false, $this->ebay_profile->id_lang);
                EbayTaskManager::addTask('update', $product, null, $this->ebay_profile->id);
            }
        }
        return $this->ebay->displayConfirmation($this->ebay->l('Settings updated'));
    }
}
