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
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
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
            $count_order_errors = EbayOrderErrors::getAll($id_ebay_profiles);
            $count_product_errors =EbayTaskManager::getErrors($id_ebay_profiles);
        }
        $category_config_list = array();

        foreach (EbayCategoryConfiguration::getEbayCategoryConfigurations($id_ebay_profiles) as $c) {
            $category_config_list[$c['id_category']] = $c;
        }

        $category_list = $this->ebay->getChildCategories(Category::getCategories($this->context->language->id), 0);
        $categories = array();

        if ($category_list) {
            $alt_row = false;
            foreach ($category_list as $category) {
                if (isset($category_config_list[$category['id_category']]['id_ebay_category'])
                    && $category_config_list[$category['id_category']]['id_ebay_category'] > 0
                ) {
                    $sql = 'SELECT p.`id_product` as id, pl.`name`, epc.`blacklisted`, epc.`extra_images`, sa.`quantity` as stock
            FROM `'._DB_PREFIX_.'product` p';

                    $sql .= Shop::addSqlAssociation('product', 'p');
                    $sql .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (p.`id_product` = pl.`id_product`
                
                AND pl.`id_lang` = '.(int) $this->ebay_profile->id_lang;
                    $sql .= Shop::addSqlRestrictionOnLang('pl');
                    $sql .= ')
            LEFT JOIN `'._DB_PREFIX_.'ebay_product_configuration` epc
                ON p.`id_product` = epc.`id_product` AND epc.id_ebay_profile = '.(int)$id_ebay_profiles.'
            LEFT JOIN `'._DB_PREFIX_.'stock_available` sa
                ON p.`id_product` = sa.`id_product`
                AND sa.`id_product_attribute` = 0
            WHERE  p.`active` = 1 AND ';
                    $sql .= ' product_shop.`id_category_default` = '.(int) $category['id_category'];
                    $sql .= StockAvailable::addSqlShopRestriction(null, null, 'sa');


                    $nb_products_blocked = 0;
                    $nb_products_variations_blocked = 0;
                    $nb_products_man = Db::getInstance()->ExecuteS($sql);
                    $nb_products_variations = 0;
                    if ($nb_products) {
                        foreach ($nb_products_man as $product_ps) {
                            $product = new Product($product_ps['id']);
                            $variation = $product->getWsCombinations();
                            $nb_products_variations += (count($variation)>0?count($variation):1);
                            if ($product_ps['blacklisted']) {
                                $nb_products_blocked += 1;
                                $nb_products_variations_blocked +=count($variation);
                            }
                        }
                    }

                     $ebay_category = EbaySynchronizer::__getEbayCategory($category['id_category'], $this->ebay_profile);


                    $categories[] = array(
                        'checked'   => ($category_config_list[$category['id_category']]['sync'] == 1 ? 1 : 0),
                        'category_multi' => $ebay_category->isMultiSku()?1 : 0,
                        'annonces' => EbayProduct::getNbProductsByCategory($this->ebay_profile->id, $category['id_category']),
                        'nb_products' => count($nb_products_man),
                        'nb_products_variations' => $nb_products_variations,
                        'nb_products_blocked' => $nb_products_blocked,
                        'nb_variations_tosync' => $nb_products_variations - $nb_products_variations_blocked,
                        'nb_product_tosync' => count($nb_products_man) - $nb_products_blocked,
                    );
                    $alt_row = !$alt_row;
                }
            }
        }
        $annonces_prevu = 0;
        foreach ($categories as $cat) {
            if($cat['checked']) {
                if($cat['category_multi']) {
                    $annonces_prevu += $cat['nb_product_tosync'];
                } else {
                    $annonces_prevu += $cat['nb_variations_tosync'];
                }
            }
        }

        $orders = EbayOrder::getOrders();
        $total = 0;
        if (!empty($orders)) {
            foreach ($orders as $ord) {
                $order = new Order($ord['id_order']);
                if (strtotime($order->date_add) > strtotime('-30 days')) {
                    $total += $order->total_paid;
                }
            }
        }

        $vars = array(
            'nb_tasks' => EbayTaskManager::getNbTasks($id_ebay_profiles),
            'count_order_errors' => ($count_order_errors?count($count_order_errors):0),
            'count_product_errors' => (EbayTaskManager::getErrors($id_ebay_profiles)?count(EbayTaskManager::getErrors($id_ebay_profiles)):0)+ count(EbayProduct::getOrphanListing($id_ebay_profiles)),
            'nb_products' => (isset($nb_products[$id_ebay_profiles]) ? $nb_products[$id_ebay_profiles] : 0),
            'dernier_import_product' => Configuration::get('DATE_LAST_SYNC_PRODUCTS'),
            'nb_categories' => count(EbayCategoryConfiguration::getEbayCategories($id_ebay_profiles)),
            'nb_products_exclu' => count(EbayProduct::getProductsBlocked($id_ebay_profiles)),
            'nb_annonces_prevu' => $annonces_prevu,
            'nb_country_shipping' => count(EbayShippingInternationalZone::getInternationalZone($id_ebay_profiles)),
            'type_sync_product' => (Configuration::get('EBAY_SYNC_PRODUCTS_BY_CRON')?'Cron':'Prestashop'),
            'ca_total' => $total,
        );
        $datetime = new DateTime(EbayConfiguration::get($id_ebay_profiles, 'EBAY_ORDER_LAST_UPDATE'));
        $vars['type_sync_order'] = (Configuration::get('EBAY_SYNC_ORDERS_BY_CRON')?'Cron':'Prestashop');

        $vars['date_last_import'] = date('Y-m-d H:i:s', strtotime($datetime->format('Y-m-d H:i:s')));
        return $this->display('dashboard.tpl', $vars);
    }
}
