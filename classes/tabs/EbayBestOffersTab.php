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

class EbayBestOffersTab extends EbayTab
{
    public function getContent($id_ebay_profile)
    {

        $url_vars = array(
            'id_tab' => '999',
            'section' => 'offers',
        );

        $url_vars['controller'] = Tools::getValue('controller');

        $url = $this->_getUrl($url_vars);
        $offers = EbayBestOffers::getOffersForProfile($id_ebay_profile);
        $fields_list = array(

            'id_product' => array(
                'title' => 'Id product',
                'type'  => 'text',
            ),
            'product_title' => array(
                'title' => 'Product Name',
                'type'  => 'text',
            ),
            'seller_message' => array(
                'title' => 'Message',
                'type'  => 'text',
            ),
            'price'     => array(
                'title' => 'Price',
                'type'  => 'text',
            ),
            'quantity'     => array(
                'title' => 'Quantity',
                'type'  => 'text',
            ),
            'status'     => array(
                'title' => 'Status',
                'type'  => 'text',
            ),
        );
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->actions = array();
        $helper->identifier = 'id_best_offer';
        $helper->show_toolbar = false;
        $helper->title = 'Best Offers';
        $helper->table = 'ebay_best_offers';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->no_link = true;


        //$currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&totlookbook_edit='.Tools::getValue('totlookbook_edit', 'global');
        $helper->currentIndex = $_SERVER['REQUEST_URI'];

        $html = "";
        $html .= $helper->generateList($offers, $fields_list);

        return $html;
    }

    public function getProductInfo($id_product)
    {
        $product = new Product($id_product, false, $this->context->language->id);

        return $product->name .'('.$id_product.')';
    }
}
