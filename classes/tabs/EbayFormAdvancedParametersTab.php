<?php
/**
 * 2007-2021 PrestaShop
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
 * @author 202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) 2007-2021 202-ecommerce
 * @license Commercial license
 *  International Registered Trademark & Property of PrestaShop SA
 */

class EbayFormAdvancedParametersTab extends EbayTab
{

    public function getContent()
    {

        // make url
        $url_vars = array(
            'id_tab' => '13',
            'section' => 'advanced_parameters',
        );

        $url_vars['controller'] = Tools::getValue('controller');

        $url = $this->_getUrl($url_vars);

        $smarty_vars = array(
            'url' => $url,
            'formAdvancedParametersController' => $this->context->link->getAdminLink('AdminFormAdvancedParameters'),
            // logs
            'api_logs'                   => Configuration::get('EBAY_API_LOGS'),
            'activate_logs'              => Configuration::get('EBAY_ACTIVATE_LOGS'),
            'is_writable'                => is_writable(_PS_MODULE_DIR_.'ebay/log/request.txt'),
            'log_file_exists'            => file_exists(_PS_MODULE_DIR_.'ebay/log/request.txt'),
            'logs_conservation_duration' => Configuration::get('EBAY_LOGS_DAYS'),

            // number of days to collect the oders for backward
            'orders_days_backward' => $this->ebay_profile->getConfiguration('EBAY_ORDERS_DAYS_BACKWARD'),
            'id_profile_ebay' => $this->ebay_profile->id,
            '_path' => $this->path,
            // send stats to eBay
            'stats' => Configuration::get('EBAY_SEND_STATS'),
        );

        return $this->display('formAdvancedParameters.tpl', $smarty_vars);
    }

    public function postProcess()
    {


        if ($this->ebay->setConfiguration('EBAY_API_LOGS', Tools::getValue('api_logs') ? 1 : 0)
            && $this->ebay->setConfiguration('EBAY_ACTIVATE_LOGS', Tools::getValue('activate_logs') ? 1 : 0)
            && Configuration::updateValue('EBAY_SEND_STATS', Tools::getValue('stats') ? 1 : 0, false, 0, 0)
            && Configuration::updateValue('EBAY_LOGS_DAYS', (int)Tools::getValue('logs_conservation_duration'), false, 0, 0)
        ) {
            if (Tools::getValue('activate_logs') == 0) {
                if (file_exists(dirname(__FILE__).'/../../log/request.txt')) {
                    unlink(dirname(__FILE__).'/../../log/request.txt');
                }
            }
            if (Tools::getValue('api_logs') == 0) {
                EbayApiLog::clear();
            }

            return $this->ebay->displayConfirmation($this->ebay->l('Settings updated'));
        } else {
            return $this->ebay->displayError($this->ebay->l('Settings failed'));
        }
    }
}
