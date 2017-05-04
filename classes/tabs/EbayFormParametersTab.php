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

class EbayFormParametersTab extends EbayTab
{
    public function getContent()
    {
        // Loading config currency
        $config_currency = new Currency((int)$this->ebay_profile->getConfiguration('EBAY_CURRENCY'));

        $url_vars = array(
            'id_tab'  => '1',
            'section' => 'parameters'
        );

        $url_vars['controller'] = Tools::getValue('controller');


        $url = $this->_getUrl($url_vars);

        $ebayShop = $this->ebay_profile->getConfiguration('EBAY_SHOP') ? $this->ebay_profile->getConfiguration('EBAY_SHOP') : $this->ebay->StoreName;

        $ebayShopValue = Tools::getValue('ebay_shop', $ebayShop);

        $ebay_country = EbayCountrySpec::getInstanceByKey($this->ebay_profile->getConfiguration('EBAY_COUNTRY_DEFAULT'));

        $createShopUrl = 'http://cgi3.ebay.'.$ebay_country->getSiteExtension().'/ws/eBayISAPI.dll?CreateProductSubscription&&productId=3&guest=1';

        $ebay_request = new EbayRequest();

        $ebay_sign_in_url = $ebay_request->getLoginUrl().'?SignIn&runame='.$ebay_request->runame.'&SessID='.$this->context->cookie->eBaySession;
        

        $ebay_paypal_email = Tools::getValue('ebay_paypal_email', $this->ebay_profile->getConfiguration('EBAY_PAYPAL_EMAIL'));
        $shopCountry = Tools::getValue('ebay_shop_country', $this->ebay_profile->getConfiguration('EBAY_SHOP_COUNTRY'));

        $user_profile = $ebay_request->getUserProfile($this->ebay_profile->ebay_user_identifier);

        $is_multishop =  Shop::isFeatureActive();


        $smarty_vars = array(
            'regenerate_token' => Configuration::get('EBAY_TOKEN_REGENERATE', null, 0, 0),
            'url'                       => $url,
            'admin_path'              => basename(_PS_ADMIN_DIR_),
            'ebay_sign_in_url'          => $ebay_sign_in_url,
            'ebay_token'                => Configuration::get('EBAY_SECURITY_TOKEN'),
            'configCurrencysign'        => $config_currency->sign,
            'catLoaded'                 => !Configuration::get('EBAY_CATEGORY_LOADED_' . $this->ebay_profile->ebay_site_id),
            'createShopUrl'             => $createShopUrl,
            'ebayCountry'               => EbayCountrySpec::getInstanceByKey($this->ebay_profile->getConfiguration('EBAY_COUNTRY_DEFAULT')),
            'ebayShopValue'             => $ebayShopValue,
            'shopPostalCode'            => Tools::getValue('ebay_shop_postalcode', $this->ebay_profile->getConfiguration('EBAY_SHOP_POSTALCODE')),
            'ebayShop'                  => $this->ebay_profile->getConfiguration('EBAY_SHOP'),
            'ebay_paypal_email'         => $ebay_paypal_email,
            'is_multishop'              => $is_multishop,
            'hasEbayBoutique'           => isset($user_profile['StoreUrl']) && !empty($user_profile['StoreUrl']) ? true : false,
            'currencies'                => TotCompatibility::getCurrenciesByIdShop($this->ebay_profile->id_shop),
            'current_currency'          => (int)$this->ebay_profile->getConfiguration('EBAY_CURRENCY'),
            'ebay_shop_countries'       => EbayCountrySpec::getCountries(false),
            'current_ebay_shop_country' => $shopCountry,
            'immediate_payment'         => (bool)$this->ebay_profile->getConfiguration('EBAY_IMMEDIATE_PAYMENT'),
            // CRON sync
            'sync_products_by_cron'      => Configuration::get('EBAY_SYNC_PRODUCTS_BY_CRON'),
            'sync_products_by_cron_url'  => $this->_getModuleUrl().'synchronizeProducts_CRON.php',
            'sync_products_by_cron_path' => $this->_getModuleUrl().'synchronizeProducts_CRON.php',
            'sync_orders_by_cron'        => Configuration::get('EBAY_SYNC_ORDERS_BY_CRON'),
            'sync_orders_by_cron_url'    => $this->_getModuleUrl().'synchronizeOrders_CRON.php',
            'sync_orders_by_cron_path'   => $this->_getModuleUrl().'synchronizeOrders_CRON.php',
            'sync_orders_returns_by_cron'        => Configuration::get('EBAY_SYNC_ORDERS_RETURNS_BY_CRON'),
            'sync_orders_returns_by_cron_url'    => $this->_getModuleUrl().'synchronizeOrdersReturns_CRON.php',
            'sync_orders_returns_by_cron_path'   => $this->_getModuleUrl().'synchronizeOrdersReturns_CRON.php',
            'activate_resynchBP'         => $this->ebay_profile->getConfiguration('EBAY_RESYNCHBP'),
            'help_Cat_upd' => array(
                'lang'           => $this->context->country->iso_code,
                'module_version' => $this->ebay->version,
                'ps_version'     => _PS_VERSION_,
                'error_code'     => 'HELP-CATEGORY-UPDATE',
            ),
            'id_shop' => $this->context->shop->id,

            'regenerate_token' => Configuration::get('EBAY_TOKEN_REGENERATE', null, 0, 0),
        );

        if (Tools::getValue('relogin')) {
            $this->ebay->login();

            $smarty_vars = array_merge($smarty_vars, array(
                'relogin'      => true,
                'redirect_url' => $ebay_request->getLoginUrl() . '?SignIn&runame=' . $ebay_request->runame . '&SessID=' . $this->context->cookie->eBaySession,
            ));
        } else {
            $smarty_vars['relogin'] = false;
        }

        if (Tools::getValue('action') == 'regenerate_token') {
            $smarty_vars['check_token_tpl'] = $this->ebay->displayCheckToken();
        }
        

        $smarty_vars['help'] = array(
            'lang'                  => $this->context->country->iso_code,
            'module_version'        => $this->ebay->version,
            'ps_version'            => _PS_VERSION_,
            'code_payment_solution' => 'HELP-SETTINGS-PAYMENT-SOLUTIONS',
        );

        return $this->display('formParameters.tpl', $smarty_vars);
    }

    public function getCurrencyId($currency)
    {
        return $currency['id_currency'];
    }

    public function postProcess()
    {

        // we retrieve the potential currencies to make sure the selected currency exists in this shop
        $currencies = TotCompatibility::getCurrenciesByIdShop($this->ebay_profile->id_shop);
        $currencies_ids = array_map(array($this, 'getCurrencyId'), $currencies);
        if(Tools::getValue('ebay_shop_postalcode') == ''){
            return $this->ebay->displayError($this->ebay->l('Code postal failed'));
        }

        if ($this->ebay_profile->setConfiguration('EBAY_PAYPAL_EMAIL', pSQL(Tools::getValue('ebay_paypal_email')))
            && $this->ebay_profile->setConfiguration('EBAY_SHOP', pSQL(Tools::getValue('ebay_shop')))
            && $this->ebay_profile->setConfiguration('EBAY_SHOP_POSTALCODE', pSQL(Tools::getValue('ebay_shop_postalcode')))
            && $this->ebay_profile->setConfiguration('EBAY_SHOP_COUNTRY', pSQL(Tools::getValue('ebay_shop_country')))

            && Configuration::updateValue('EBAY_SYNC_PRODUCTS_BY_CRON', ('cron' === Tools::getValue('sync_products_mode')))
            && Configuration::updateValue('EBAY_SYNC_ORDERS_BY_CRON', ('cron' === Tools::getValue('sync_orders_mode')))
            && $this->ebay_profile->setConfiguration('EBAY_RESYNCHBP', Tools::getValue('activate_resynchBP') ? 1 : 0)
            && Configuration::updateValue('EBAY_SYNC_ORDERS_RETURNS_BY_CRON', ('cron' === Tools::getValue('sync_orders_returns_mode')))
            && in_array((int) Tools::getValue('currency'), $currencies_ids)
            && $this->ebay_profile->setConfiguration('EBAY_CURRENCY', (int) Tools::getValue('currency'))
            && $this->ebay_profile->setConfiguration('EBAY_IMMEDIATE_PAYMENT', (int) Tools::getValue('immediate_payment'))
        ) {
            $products = EbayProduct::getProductsWithoutBlacklisted($this->ebay_profile->id_lang, $this->ebay_profile->id, true);

                foreach ($products as $product_id) {
                    $product = new Product($product_id['id_product'], false, $this->ebay_profile->id_lang);
                    EbayTaskManager::addTask('update',$product, null, $this->ebay_profile->id);
                }

                $link = new Link();
                $url = $link->getAdminLink('AdminModules');
            $this->ebay_profile->setConfiguration('EBAY_PARAMETERS_TAB_OK', 1);
            Tools::redirectAdmin($url.'&configure=ebay&module_name=ebay&id_tab=2&section=category#dashbord');

        } else {
            return $this->ebay->displayError($this->ebay->l('Settings failed'));
        }
    }


}
