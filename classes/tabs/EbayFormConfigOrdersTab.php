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
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class EbayFormConfigOrdersTab extends EbayTab
{
    public function getContent()
    {

        $url_vars = array(
            'id_tab'  => '102',
            'section' => 'parametersorders'
        );

        $url_vars['controller'] = Tools::getValue('controller');

        if (!$this->ebay_profile->getConfiguration('EBAY_ANONNCES_CONFIG_TAB_OK')) {
            $vars = array(
                'msg' => $this->ebay->l('Please configure the \'Listing settings\' tab before using this tab', 'ebayformeconfigannoncestab'),
            );
            return $this->display('alert_tabs.tpl', $vars);
        }

        $url = $this->_getUrl($url_vars);

        $ebay_request = new EbayRequest();

        $ebay_sign_in_url = $ebay_request->getLoginUrl().'?SignIn&runame='.$ebay_request->runame.'&SessID='.$this->context->cookie->eBaySession;

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
            'send_tracking_code'        => (bool)$this->ebay_profile->getConfiguration('EBAY_SEND_TRACKING_CODE'),
            'order_states'              => $order_states,
            'current_order_state'       => $current_order_state,
            'current_order_return_state' => $this->ebay_profile->getConfiguration('EBAY_RETURN_ORDER_STATE'),
            'orders_days_backward' => $this->ebay_profile->getConfiguration('EBAY_ORDERS_DAYS_BACKWARD'),
            'id_profile_ebay' => $this->ebay_profile->id,
            '_path' => $this->path,

        );

        return $this->display('formParametersOrders.tpl', $smarty_vars);
    }


    public function postProcess()
    {
        if ($this->ebay_profile->setConfiguration('EBAY_SEND_TRACKING_CODE', (int) Tools::getValue('send_tracking_code'))
            && $this->ebay_profile->setConfiguration('EBAY_SHIPPED_ORDER_STATE', (int) Tools::getValue('shipped_order_state'))
            && $this->ebay_profile->setConfiguration('EBAY_RETURN_ORDER_STATE', (int) Tools::getValue('return_order_state'))
            && $this->ebay_profile->setConfiguration('EBAY_ORDERS_DAYS_BACKWARD', (int)Tools::getValue('orders_days_backward'), false, 0, 0)
        ) {
            $link = new Link();
            $url = $link->getAdminLink('AdminModules');
            $this->ebay_profile->setConfiguration('EBAY_ORDERS_CONFIG_TAB_OK', 1);
            //Tools::redirectAdmin($url.'&configure=ebay&module_name=ebay&id_tab=102&section=category#dashboard');
            return $this->ebay->displayConfirmation($this->ebay->l('Settings updated'));
        } else {
            return $this->ebay->displayError($this->ebay->l('Settings failed'));
        }
    }
}
