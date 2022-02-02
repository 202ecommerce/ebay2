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
 *
 */

class EbayOrdersTab extends EbayTab
{
    private $vars;
    public function getContent($id_ebay_profile)
    {
        $this->vars = array();
        $pagination_vars = EbayOrder::getPaginatedOrdersErrors($id_ebay_profile);
        $pagination_vars["all_orders"] = array_reverse($pagination_vars["all_orders"]);
        $this->vars = array_merge($this->vars, $pagination_vars);
        $this->vars['id_ebay_profile'] = $id_ebay_profile;
        $this->vars['ebay_token'] = Configuration::get('EBAY_SECURITY_TOKEN');
        $this->vars['ebayOrdersController'] = $this->context->link->getAdminLink('AdminEbayOrders');
        $url_vars = array(
            'id_tab' => '6',
            'section' => 'orders',
        );
        $url_vars['controller'] = Tools::getValue('controller');
        $datetime = new DateTime(Configuration::get('EBAY_ORDER_LAST_UPDATE'));
        $this->vars['type_sync_order'] = (Configuration::get('EBAY_SYNC_ORDERS_BY_CRON')?'Cron':'Prestashop');
        $this->vars['tpl_orders_ajax'] = _PS_MODULE_DIR_ . 'ebay/views/templates/hook/tableOrders_ajax.tpl';
        $this->vars['date_last_import'] = date('Y-m-d H:i:s', strtotime($datetime->format('Y-m-d H:i:s')));
        $this->vars['currency'] = $this->context->currency;
        $this->vars['url'] = $this->_getUrl($url_vars);
        return $this->display('tableOrders.tpl', $this->vars);
    }
}
