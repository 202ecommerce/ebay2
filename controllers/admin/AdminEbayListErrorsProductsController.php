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
 *  @copyright 2007-2018 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class AdminEbayListErrorsProductsController extends ModuleAdminController
{

    public function ajaxProcessExclureProductAjax()
    {
        EbayProductConfiguration::insertOrUpdate(Tools::getValue('id_product'), array(
            'id_ebay_profile' => Tools::getValue('id_ebay_profile'),
            'blacklisted' =>  1,
            'extra_images' => 0,
        ));
        EbayTaskManager::deleteTaskForPorduct(Tools::getValue('id_product'));
        $ebay_profile = new EbayProfile(Tools::getValue('id_ebay_profile'));
        if (EbayProduct::getIdProductRef(Tools::getValue('id_product'), $ebay_profile->ebay_user_identifier, $ebay_profile->ebay_site_id)) {
            $product = new Product(Tools::getValue('id_product'));
            EbayTaskManager::addTask('mod', $product, Tools::getValue('id_employee'), Tools::getValue('id_ebay_profile'));
            EbayTaskManager::deleteTaskForOutOfStock(Tools::getValue('id_product'), Tools::getValue('id_ebay_profile'));
        }
    }

    public function ajaxProcessPaginationProductErrors()
    {
        $token_for_product = Tools::getValue('token_for_product');
        $page_current = Tools::getValue('page') ? Tools::getValue('page') : 1;
        $length = Tools::getValue('length') ? Tools::getValue('length') : 20;
        $id_ebay_profile = Tools::getValue('profile');

        $search = array(
            'id_product' => Tools::getValue('id_product'),
            'name_product' => Tools::getValue('name_product'),
        );

        $ebay = Module::getInstanceByName('ebay');
        $context = Context::getContext();
        $ebayErrorsProducts = new EbayListErrorsProductsTab($ebay, $context->smarty, $context);
        $response = $ebayErrorsProducts->getContent($id_ebay_profile, $page_current, $length, $token_for_product, $search);
        echo $response;
        die();
    }

    public function ajaxProcessRefreshErrors()
    {
        $id_ebay_profile = Tools::getValue('id_ebay_profile');
        $token_for_product = Tools::getValue('token_for_product');
        $ebay = Module::getInstanceByName('ebay');
        $context = Context::getContext();
        $ebayErrorsProducts = new EbayListErrorsProductsTab($ebay, $context->smarty, $context);
        $response = $ebayErrorsProducts->getContent($id_ebay_profile, 1, 20, $token_for_product);
        echo $response;
        die();
    }
}
