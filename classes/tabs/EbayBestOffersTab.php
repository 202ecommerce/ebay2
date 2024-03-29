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
class EbayBestOffersTab extends EbayTab
{
    public function getContent($id_ebay_profile)
    {
        $url_vars = [
            'id_tab' => '999',
            'section' => 'offers',
        ];

        $url_vars['controller'] = Tools::getValue('controller');

        $url = $this->_getUrl($url_vars);
        $offers = EbayBestOffers::getOffersForProfile($id_ebay_profile);
        $fields_list = [
            'id_product' => [
                'title' => 'Id product',
                'type' => 'text',
            ],
            'product_title' => [
                'title' => 'Product Name',
                'type' => 'text',
            ],
            'seller_message' => [
                'title' => 'Message',
                'type' => 'text',
            ],
            'price' => [
                'title' => 'Price',
                'type' => 'text',
            ],
            'quantity' => [
                'title' => 'Quantity',
                'type' => 'text',
            ],
            'status' => [
                'title' => 'Status',
                'type' => 'text',
            ],
        ];
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->actions = [];
        $helper->identifier = 'id_best_offer';
        $helper->show_toolbar = false;
        $helper->title = 'Best Offers';
        $helper->table = 'ebay_best_offers';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->no_link = true;

        //$currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&totlookbook_edit='.Tools::getValue('totlookbook_edit', 'global');
        $helper->currentIndex = $_SERVER['REQUEST_URI'];

        $html = '';
        $html .= $helper->generateList($offers, $fields_list);

        return $html;
    }

    public function getProductInfo($id_product)
    {
        $product = new Product($id_product, false, $this->context->language->id);

        return $product->name . '(' . $id_product . ')';
    }
}
