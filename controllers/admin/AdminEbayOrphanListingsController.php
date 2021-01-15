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

class AdminEbayOrphanListingsController extends ModuleAdminController
{
    public function ajaxProcessLoadTableOrphanListings()
    {
        if (version_compare(_PS_VERSION_, '1.5', '>=') && Tools::getValue('id_shop')) {
            $context = Context::getContext();
            $context->shop = new Shop((int)Tools::getValue('id_shop'));
        }

        $page_current = Tools::getValue('page') ? Tools::getValue('page') : 1;
        $length = 20;
        $count_orphans_product = EbayProduct::getCountOrphanListing(Tools::getValue('profile'));
        $final_res = EbayProduct::getOrphanListing(Tools::getValue('profile'), $page_current, $length);
        $pages_all = ceil(((int)$count_orphans_product[0]['number']) / ((int)$length));
        $range = 3;
        $start = $page_current - $range;
        if ($start <= 0) {
            $start = 1;
        }

        $stop = $page_current + $range;

        if ($stop > $pages_all) {
            $stop = $pages_all;
        }

        $prev_page = (int)$page_current - 1;
        $next_page = (int)$page_current + 1;
        $tpl_include = _PS_MODULE_DIR_ . 'ebay/views/templates/hook/pagination.tpl';


        $context = Context::getContext();

// Smarty
        $template_vars = array(
            'id_ebay_profile' => Tools::getValue('profile'),
            'id_employee' => Tools::getValue('id_employee'),
            'ads' => $final_res,
            'count' => count($final_res),
        );

// Smarty datas
        $ebay = Module::getInstanceByName('ebay');
        $context->smarty->assign($template_vars);
        $context->smarty->assign(array(
            'prev_page' => $prev_page,
            'next_page' => $next_page,
            'tpl_include' => $tpl_include,
            'pages_all' => $pages_all,
            'page_current' => $page_current,
            'start' => $start,
            'stop' => $stop,
        ));

        die($ebay->display(realpath(dirname(__FILE__) . '/../../'), '/views/templates/hook/table_orphan_listings.tpl'));
    }

    public function ajaxProcessDeleteOrphanListing()
    {
        $id_product_ref = Tools::getValue('id_product_ref');

        $product = EbayProduct::getProductsIdFromItemId($id_product_ref);
        if (Tools::getValue('listing_action') == "end") {
            EbayTaskManager::deleteTaskForPorductAndEbayProfile($product['id_product'], $product['id_ebay_profile']);
            $product_ps = new Product($product['id_product'], false, Tools::getValue('id_lang'));
            EbayTaskManager::addTask('end', $product_ps, Tools::getValue('id_employee'), $product['id_ebay_profile'], $product['id_product_attribute']);
        }

        if (Tools::getValue('listing_action') == "out_of_stock") {
            EbayProductConfiguration::insertOrUpdate($product['id_product'], array(
                'id_ebay_profile' => $product['id_ebay_profile'],
                'blacklisted' =>  1,
                'extra_images' => 0,
            ));
            EbayTaskManager::deleteTaskForPorduct($product['id_product']);
            if ($product) {
                $productPS = new Product($product['id_product']);
                EbayTaskManager::addTask('mod', $productPS, Tools::getValue('id_employee'), Tools::getValue('id_ebay_profile'), $product['id_product_attribute']);
                EbayTaskManager::deleteTaskForOutOfStock($product['id_product'], $product['id_ebay_profile']);
            }
        }
        echo '1';
        die();
    }

    public function ajaxProcessDeleteAllOrphanListing()
    {
        $id_profile = Tools::getValue('id_ebay_profile');
        $products = EbayProduct::getAllOrphanProductsId($id_profile);
        foreach ($products as $product) {
            EbayTaskManager::deleteTaskForPorductAndEbayProfile($product['id_product'], $product['id_ebay_profile']);
            $product_ps = new Product($product['id_product'], false, Tools::getValue('id_lang'));
            EbayTaskManager::addTask('end', $product_ps, Tools::getValue('id_employee'), $product['id_ebay_profile'], $product['id_product_attribute']);
        }
        die('1');
    }
}
