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
        
        $returns_policy_configuration = $this->ebay_profile->getReturnsPolicyConfiguration();

        $returnsConditionAccepted = Tools::getValue('ebay_returns_accepted_option', Configuration::get('EBAY_RETURNS_ACCEPTED_OPTION'));

        $ebay_paypal_email = Tools::getValue('ebay_paypal_email', $this->ebay_profile->getConfiguration('EBAY_PAYPAL_EMAIL'));
        $shopCountry = Tools::getValue('ebay_shop_country', $this->ebay_profile->getConfiguration('EBAY_SHOP_COUNTRY'));
        $ebayListingDuration = $this->ebay_profile->getConfiguration('EBAY_LISTING_DURATION') ? $this->ebay_profile->getConfiguration('EBAY_LISTING_DURATION') : 'GTC';

        $user_profile = $ebay_request->getUserProfile($this->ebay_profile->ebay_user_identifier);

        $is_multishop =  Shop::isFeatureActive();

        $order_states = OrderState::getOrderStates($this->ebay_profile->id_lang);
        
        $current_order_state = $this->ebay_profile->getConfiguration('EBAY_SHIPPED_ORDER_STATE');
        if ($current_order_state === null) {
            foreach ($order_states as $order_state) {
                if ($order_state['template'] === 'shipped') {
                    // NDRArbuz: is this the best way to find it with no doubt?
                    $current_order_state = $order_state['id_order_state'];
                    break;
                }
            }
        }

        $smarty_vars = array(
            'url'                       => $url,
            'ebay_sign_in_url'          => $ebay_sign_in_url,
            'ebay_token'                => Configuration::get('EBAY_SECURITY_TOKEN'),
            'configCurrencysign'        => $config_currency->sign,
            'policies'                  => EbayReturnsPolicy::getReturnsPolicies(),
            'catLoaded'                 => !Configuration::get('EBAY_CATEGORY_LOADED_' . $this->ebay_profile->ebay_site_id),
            'createShopUrl'             => $createShopUrl,
            'ebayCountry'               => EbayCountrySpec::getInstanceByKey($this->ebay_profile->getConfiguration('EBAY_COUNTRY_DEFAULT')),
            'ebayReturns'               => preg_replace('#<br\s*?/?>#i', "\n", $this->ebay_profile->getReturnsPolicyConfiguration()->ebay_returns_description),
            'ebayShopValue'             => $ebayShopValue,
            'shopPostalCode'            => Tools::getValue('ebay_shop_postalcode', $this->ebay_profile->getConfiguration('EBAY_SHOP_POSTALCODE')),
            'listingDurations'          => $this->_getListingDurations(),
            'ebayShop'                  => $this->ebay_profile->getConfiguration('EBAY_SHOP'),
            'returnsConditionAccepted'  => Tools::getValue('ebay_returns_accepted_option', $returns_policy_configuration->ebay_returns_accepted_option),
            'automaticallyRelist'       => $this->ebay_profile->getConfiguration('EBAY_AUTOMATICALLY_RELIST'),
            'ebay_paypal_email'         => $ebay_paypal_email,
            'returnsConditionAccepted'  => $returnsConditionAccepted,
            'ebayListingDuration'       => $ebayListingDuration,
            'is_multishop'              => $is_multishop,
            'within_values'             => unserialize(Configuration::get('EBAY_RETURNS_WITHIN_VALUES')),
            'within'                    => $returns_policy_configuration->ebay_returns_within,
            'whopays_values'            => unserialize(Configuration::get('EBAY_RETURNS_WHO_PAYS_VALUES')),
            'whopays'                   => $returns_policy_configuration->ebay_returns_who_pays,
            'activate_mails'            => Configuration::get('EBAY_ACTIVATE_MAILS'),
            'hasEbayBoutique'           => isset($user_profile['StoreUrl']) && !empty($user_profile['StoreUrl']) ? true : false,
            'currencies'                => TotCompatibility::getCurrenciesByIdShop($this->ebay_profile->id_shop),
            'current_currency'          => (int)$this->ebay_profile->getConfiguration('EBAY_CURRENCY'),
            'ebay_shop_countries'       => EbayCountrySpec::getCountries(false),
            'current_ebay_shop_country' => $shopCountry,
            'send_tracking_code'        => (bool)$this->ebay_profile->getConfiguration('EBAY_SEND_TRACKING_CODE'),
            'order_states'              => $order_states,
            'current_order_state'       => $current_order_state,
            'current_order_return_state' => $this->ebay_profile->getConfiguration('EBAY_RETURN_ORDER_STATE'),
            'immediate_payment'         => (bool)$this->ebay_profile->getConfiguration('EBAY_IMMEDIATE_PAYMENT'),
            //EAN
            'synchronize_ean'    => (string)Configuration::get('EBAY_SYNCHRONIZE_EAN'),
            'synchronize_mpn'    => (string)Configuration::get('EBAY_SYNCHRONIZE_MPN'),
            'synchronize_upc'    => (string)Configuration::get('EBAY_SYNCHRONIZE_UPC'),
            'synchronize_isbn'   => (string)Configuration::get('EBAY_SYNCHRONIZE_ISBN'),
            'ean_not_applicable' => (int)Configuration::get('EBAY_EAN_NOT_APPLICABLE'),
            'help_ean' => array(
                'lang'           => $this->context->country->iso_code,
                'module_version' => $this->ebay->version,
                'ps_version'     => _PS_VERSION_,
                'error_code'     => 'HELP-ADV-SETTINGS-EAN',
            ),
            'help_gtc' => array(
                'lang'           => $this->context->country->iso_code,
                'module_version' => $this->ebay->version,
                'ps_version'     => _PS_VERSION_,
                'error_code'     => 'HELP-SETTINGS-LISTING-DURATION',
            ),
            'id_shop' => $this->context->shop->id,
            'out_of_stock_value' => (bool)EbayConfiguration::get($this->ebay_profile->id, 'EBAY_OUT_OF_STOCK'),
            'help_out_of_stock' => array(
                'lang'           => $this->context->country->iso_code,
                'module_version' => $this->ebay->version,
                'ps_version'     => _PS_VERSION_,
                'error_code'     => 'HELP-SETTINGS-OUT-OF-STOCK',
            ),
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
        $this->ebay->setConfiguration('EBAY_SYNCHRONIZE_EAN', (string)Tools::getValue('synchronize_ean'));
        $this->ebay->setConfiguration('EBAY_SYNCHRONIZE_MPN', (string)Tools::getValue('synchronize_mpn'));
        $this->ebay->setConfiguration('EBAY_SYNCHRONIZE_UPC', (string)Tools::getValue('synchronize_upc'));
        $this->ebay->setConfiguration('EBAY_SYNCHRONIZE_ISBN', (string)Tools::getValue('synchronize_isbn'));
        $this->ebay->setConfiguration('EBAY_EAN_NOT_APPLICABLE', Tools::getValue('ean_not_applicable') ? 1 : 0);


        if ($this->ebay_profile->setConfiguration('EBAY_PAYPAL_EMAIL', pSQL(Tools::getValue('ebay_paypal_email')))
            && $this->ebay_profile->setConfiguration('EBAY_SHOP', pSQL(Tools::getValue('ebay_shop')))
            && $this->ebay_profile->setConfiguration('EBAY_SHOP_POSTALCODE', pSQL(Tools::getValue('ebay_shop_postalcode')))
            && $this->ebay_profile->setConfiguration('EBAY_SHOP_COUNTRY', pSQL(Tools::getValue('ebay_shop_country')))
            && $this->ebay_profile->setConfiguration('EBAY_LISTING_DURATION', Tools::getValue('listingdurations'))
            && $this->ebay_profile->setConfiguration('EBAY_AUTOMATICALLY_RELIST', Tools::getValue('automaticallyrelist'))
            && $this->ebay_profile->setReturnsPolicyConfiguration(
                pSQL(Tools::getValue('returnswithin')),
                pSQL(Tools::getValue('returnswhopays')),
                (Tools::nl2br(Tools::getValue('ebay_returns_description'))),
                pSQL(Tools::getValue('ebay_returns_accepted_option'))
            )
            && $this->ebay->setConfiguration('EBAY_ACTIVATE_MAILS', Tools::getValue('activate_mails') ? 1 : 0)
            && in_array((int) Tools::getValue('currency'), $currencies_ids)
            && $this->ebay_profile->setConfiguration('EBAY_CURRENCY', (int) Tools::getValue('currency'))
            && $this->ebay_profile->setConfiguration('EBAY_SEND_TRACKING_CODE', (int) Tools::getValue('send_tracking_code'))
            && $this->ebay_profile->setConfiguration('EBAY_SHIPPED_ORDER_STATE', (int) Tools::getValue('shipped_order_state'))
            && $this->ebay_profile->setConfiguration('EBAY_RETURN_ORDER_STATE', (int) Tools::getValue('return_order_state'))
            && $this->ebay_profile->setConfiguration('EBAY_IMMEDIATE_PAYMENT', (int) Tools::getValue('immediate_payment'))
        ) {

                $link = new Link();
                $url = $link->getAdminLink('AdminModules');

            Tools::redirectAdmin($url.'&configure=ebay&module_name=ebay&id_tab=2&section=category#dashbord');
        } else {
            return $this->ebay->displayError($this->ebay->l('Settings failed'));
        }
    }

    private function _getListingDurations()
    {
        return array(
            'Days_1'  => $this->ebay->l('1 Day'),
            'Days_3'  => $this->ebay->l('3 Days'),
            'Days_5'  => $this->ebay->l('5 Days'),
            'Days_7'  => $this->ebay->l('7 Days'),
            'Days_10' => $this->ebay->l('10 Days'),
            'Days_30' => $this->ebay->l('30 Days'),
            'GTC'     => $this->ebay->l('Good \'Till Canceled')
        );
    }
}
