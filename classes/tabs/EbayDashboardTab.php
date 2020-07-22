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
 * @author 202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) 2017-2020 202-ecommerce
 * @license Commercial license
 *  International Registered Trademark & Property of PrestaShop SA
 */

class EbayDashboardTab extends EbayTab
{

    public function getContent($id_ebay_profiles)
    {
        $nb_products = EbayProduct::getNbProductsByIdEbayProfiles(array($id_ebay_profiles));
        $count_order_errors = false;
        $count_product_errors = false;


        if ($id_ebay_profiles) {
            $count_order_errors = EbayOrderErrors::getAllCount($id_ebay_profiles);
            $count_product_errors =EbayTaskManager::getErrorsCount($id_ebay_profiles);
        }


        $total = EbayOrder::getSumOrders();
        $total = $total[0]['sum'] ? $total[0]['sum'] : 0;
        $count_orphan_listing = EbayProduct::getCountOrphanListing($this->ebay_profile->id);
        $count_orphan_listing = $count_orphan_listing[0]['number'];

        $vars = array(
            'nb_tasks' => EbayTaskManager::getNbTasks($id_ebay_profiles),
            'count_order_errors' => (isset($count_order_errors[0]['nb'])?$count_order_errors[0]['nb']:0),
            'count_product_errors' => (isset($count_product_errors[0]['nb'])?$count_product_errors[0]['nb']:0)+ (int) $count_orphan_listing,
            'nb_products' => EbayProduct::getNbProducts($id_ebay_profiles),
            'dernier_import_product' => Configuration::get('DATE_LAST_SYNC_PRODUCTS'),
            'nb_categories' => EbayCategoryConfiguration::getNbPrestashopCategories($this->ebay_profile->id),
            'nb_products_exclu' => count(EbayProduct::getProductsBlocked($id_ebay_profiles)),
            'country_shipping' => EbayShippingInternationalZone::getInternationalZone($id_ebay_profiles),
            'type_sync_product' => (Configuration::get('EBAY_SYNC_PRODUCTS_BY_CRON')?'Cron':'Prestashop'),
            'ca_total' => $total,
        );

        $datetime = new DateTime(EbayConfiguration::get($id_ebay_profiles, 'EBAY_ORDER_LAST_UPDATE'));
        $vars['type_sync_order'] = (Configuration::get('EBAY_SYNC_ORDERS_BY_CRON')?'Cron':'Prestashop');

        $vars['date_last_import'] = date('Y-m-d H:i:s', strtotime($datetime->format('Y-m-d H:i:s')));
        return $this->display('dashboard.tpl', $vars);
    }
}
