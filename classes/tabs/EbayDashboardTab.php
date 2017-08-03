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
            $count_order_errors = EbayOrderErrors::getAllCount($id_ebay_profiles);
            $count_product_errors =EbayTaskManager::getErrorsCount($id_ebay_profiles);
        }
        $category_config_list = array();

        foreach (EbayCategoryConfiguration::getEbayCategoryConfigurations($id_ebay_profiles) as $c) {
            $category_config_list[$c['id_category']] = $c;
        }

        $category_list = $this->ebay->getChildCategories(Category::getCategories($this->context->language->id), 0);

        $id_categories = array();
        if ($category_list) {
            foreach ($category_list as $category) {
                if (isset($category_config_list[$category['id_category']]['id_ebay_category'])
                    && $category_config_list[$category['id_category']]['id_ebay_category'] > 0
                ) {
                    $id_categories[] = (int)$category['id_category'];
                }
            }
            if (!empty($id_categories)) {
                $id_categories_str = implode(',', $id_categories);

                $sql_for_variations = 'SELECT COUNT(pa.id_product_attribute) as nb_variations
            FROM ' . _DB_PREFIX_ . 'product_attribute pa
			' . Shop::addSqlAssociation('product_attribute', 'pa') .
                    ' LEFT JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = pa.id_product ';

                $sql_for_variations .= Shop::addSqlAssociation('product', 'p');
                $sql_for_variations .= '
            LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa
                ON p.`id_product` = sa.`id_product`
                AND sa.`id_product_attribute` = 0
            LEFT JOIN `'._DB_PREFIX_.'ebay_category_configuration` ecc 
                ON ecc.id_category = product_shop.`id_category_default`
            LEFT JOIN `'._DB_PREFIX_.'ebay_category` ec 
                ON ec.id_ebay_category = ecc.id_ebay_category
            WHERE  p.`active` = 1 AND ';
                $sql_for_variations .= ' ecc.sync=1 AND ec.is_multi_sku IS NULL AND ';
                $sql_for_variations .= ' product_shop.`id_category_default` IN (' . $id_categories_str . ')';
                $sql_for_variations .= StockAvailable::addSqlShopRestriction(null, null, 'sa');


                $res_variations = Db::getInstance()->ExecuteS($sql_for_variations);

                $sql_for_variations_blocked = 'SELECT COUNT(pa.`id_product_attribute`) as nb_variations
            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
			' . Shop::addSqlAssociation('product_attribute', 'pa') .
                    ' LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.id_product = pa.id_product ';
                $sql_for_variations_blocked .= Shop::addSqlAssociation('product', 'p');
                $sql_for_variations_blocked .= '
            LEFT JOIN `' . _DB_PREFIX_ . 'ebay_product_configuration` epc
                ON p.`id_product` = epc.`id_product` AND epc.id_ebay_profile = ' . (int)$id_ebay_profiles;
                $sql_for_variations_blocked .= ' LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa
                ON p.`id_product` = sa.`id_product`
                AND sa.`id_product_attribute` = 0
            LEFT JOIN `'._DB_PREFIX_.'ebay_category_configuration` ecc 
                ON ecc.id_category = product_shop.`id_category_default`
            LEFT JOIN `'._DB_PREFIX_.'ebay_category` ec 
                ON ec.id_ebay_category = ecc.id_ebay_category
            WHERE  p.`active` = 1 AND epc.`blacklisted`=1 AND ';
                $sql_for_variations_blocked .= ' ecc.sync=1 AND ec.is_multi_sku IS NULL AND ';
                $sql_for_variations_blocked .= ' product_shop.`id_category_default` IN (' . $id_categories_str . ')';
                $sql_for_variations_blocked .= StockAvailable::addSqlShopRestriction(null, null, 'sa');


                $res_variations_blocked = Db::getInstance()->ExecuteS($sql_for_variations_blocked);

                $sql_products_blocked = 'SELECT  COUNT(epc.`blacklisted`) AS num_products
            FROM `' . _DB_PREFIX_ . 'product` p';

                $sql_products_blocked .= Shop::addSqlAssociation('product', 'p');
                $sql_products_blocked .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (p.`id_product` = pl.`id_product`
                
                AND pl.`id_lang` = ' . (int)$this->ebay_profile->id_lang;
                $sql_products_blocked .= Shop::addSqlRestrictionOnLang('pl');
                $sql_products_blocked .= ')
            LEFT JOIN `' . _DB_PREFIX_ . 'ebay_product_configuration` epc
                ON p.`id_product` = epc.`id_product` AND epc.id_ebay_profile = ' . (int)$id_ebay_profiles . '
            LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa
                ON p.`id_product` = sa.`id_product`
                AND sa.`id_product_attribute` = 0
            LEFT JOIN `'._DB_PREFIX_.'ebay_category_configuration` ecc 
                ON ecc.id_category = product_shop.`id_category_default`
            LEFT JOIN `'._DB_PREFIX_.'ebay_category` ec 
                ON ec.id_ebay_category = ecc.id_ebay_category
            WHERE  p.`active` = 1 AND epc.`blacklisted`=1 AND ';
                $sql_products_blocked .= ' ecc.sync=1 AND ec.is_multi_sku = 1 AND ';
                $sql_products_blocked .= ' product_shop.`id_category_default` IN (' . $id_categories_str . ')';
                $sql_products_blocked .= StockAvailable::addSqlShopRestriction(null, null, 'sa');


                $res_products_blocked = Db::getInstance()->ExecuteS($sql_products_blocked);

                $sql_num_products = 'SELECT  COUNT(p.id_product) AS num_products
            FROM `' . _DB_PREFIX_ . 'product` p';

                $sql_num_products .= Shop::addSqlAssociation('product', 'p');
                $sql_num_products .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (p.`id_product` = pl.`id_product`
                
                AND pl.`id_lang` = ' . (int)$this->ebay_profile->id_lang;
                $sql_num_products .= Shop::addSqlRestrictionOnLang('pl');
                $sql_num_products .= ')
            LEFT JOIN `' . _DB_PREFIX_ . 'ebay_product_configuration` epc
                ON p.`id_product` = epc.`id_product` AND epc.id_ebay_profile = ' . (int)$id_ebay_profiles . '
            LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa
                ON p.`id_product` = sa.`id_product`
                AND sa.`id_product_attribute` = 0
            LEFT JOIN `'._DB_PREFIX_.'ebay_category_configuration` ecc 
                ON ecc.id_category = product_shop.`id_category_default`
            LEFT JOIN `'._DB_PREFIX_.'ebay_category` ec 
                ON ec.id_ebay_category = ecc.id_ebay_category
            WHERE  p.`active` = 1 AND ';
                $sql_num_products .= ' ecc.sync=1 AND ec.is_multi_sku = 1 AND ';
                $sql_num_products .= ' product_shop.`id_category_default` IN (' . $id_categories_str . ')';
                $sql_num_products .= StockAvailable::addSqlShopRestriction(null, null, 'sa');


                $res_num_products = Db::getInstance()->ExecuteS($sql_num_products); 



            }
            $nb_products = (int) isset($res_num_products[0]['num_products'])?$res_num_products[0]['num_products']:0;
            $products_variations = (int) isset($res_variations[0]['nb_variations'])?$res_variations[0]['nb_variations']:0;
            $products_blocked = (int) isset($res_products_blocked[0]['num_products'])?$res_products_blocked[0]['num_products']:0;
            $products_variations_blocked = (int) isset($res_variations_blocked[0]['nb_variations'])?$res_variations_blocked[0]['nb_variations']:0;

        }
        $annonces_prevu = $nb_products+$products_variations-($products_blocked+$products_variations_blocked);

        $total = EbayOrder::getSumOrders();
        $total = $total[0]['sum'] ? $total[0]['sum'] : 0;
        $count_orphan_listing = EbayProduct::getCountOrphanListing($this->ebay_profile->id);
        $count_orphan_listing = $count_orphan_listing[0]['number'];

        $vars = array(
            'nb_tasks' => EbayTaskManager::getNbTasks($id_ebay_profiles),
            'count_order_errors' => (isset($count_order_errors[0]['nb'])?$count_order_errors[0]['nb']:0),
            'count_product_errors' => (isset($count_product_errors[0]['nb'])?$count_product_errors[0]['nb']:0)+ (int) $count_orphan_listing,
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
