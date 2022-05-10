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
class AdminEbayListingsController extends ModuleAdminController
{
    public function ajaxProcessGetEbayListings()
    {
        if (Tools::getValue('id_shop')) {
            $context = Context::getContext();
            $context->shop = new Shop((int) Tools::getValue('id_shop'));
        }
        $page_current = Tools::getValue('page') ? Tools::getValue('page') : 1;
        $length = Tools::getValue('length') ? Tools::getValue('length') : 20;

        $search = [
            'id_product' => Tools::getValue('id_prod'),
            'id_product_ebay' => Tools::getValue('id_prod_ebay'),
            'name_product' => Tools::getValue('name_prod'),
            'name_cat' => Tools::getValue('name_cat'),
        ];

        $ebay = Module::getInstanceByName('ebay');
        $ebay->displayEbayListingsAjax(Tools::getValue('admin_path'), $search, (int) Tools::getValue('id_employee'), $page_current, $length);
        exit();
    }
}
