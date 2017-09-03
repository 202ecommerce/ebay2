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

if (!defined('TMP_DS')) {
    define('TMP_DS', DIRECTORY_SEPARATOR);
}

require_once dirname(__FILE__).TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'config'.TMP_DS.'config.inc.php';

if (!Tools::getValue('token') || Tools::getValue('token') != Configuration::get('EBAY_SECURITY_TOKEN')) {
    die('ERROR: Invalid Token');
}
function getSelectors($ref_categories, $id_category_ref, $id_category, $level, $ebay)
{

    $var = null;
    $context = Context::getContext();
    $ebay = new Ebay();

    if ((int)$level > 1) {
        foreach ($ref_categories as $ref_id_category_ref => $category) {
            if ($ref_id_category_ref == $id_category_ref) {
                if (isset($ref_categories[$category['id_category_ref_parent']]['children'])) {
                    if ((int) $category['id_category_ref'] != (int) $category['id_category_ref_parent']) {
                        $var .= getSelectors($ref_categories, (int) $category['id_category_ref_parent'], (int) $id_category, (int) ($level - 1), $ebay);
                    }
                    $tpl_var = array(
                        "level" => $level,
                        "id_category" => $id_category,
                        "category" => $category,
                        "ref_categories" => $ref_categories,
                    );
                    $context->smarty->assign($tpl_var);
                    $var .=  $ebay->display(realpath(dirname(__FILE__).'/../'), '/views/templates/hook/form_select_categories.tpl');
                }
            }
        }
    } else {
        $valid_categories = array();
        foreach ($ref_categories as $ref_id_category_ref => $category) {
            if (isset($category['id_category_ref']) && (int) $category['id_category_ref'] == (int) $category['id_category_ref_parent'] && !empty($category['id_ebay_category'])) {
                $valid_categories[$ref_id_category_ref] = $category;
            }
        }

        $tpl_var = array(
            "level" => $level,
            "id_category" => $id_category,
            "ch_cat_str" => Tools::safeOutput(Tools::getValue('ch_cat_str')),
            "ref_categories" => $valid_categories,
            "id_category_ref" => $id_category_ref
        );
        $context->smarty->assign($tpl_var);
        $var .=  $ebay->display(realpath(dirname(__FILE__).'/../'), '/views/templates/hook/form_select_categories.tpl');
    }

    return $var;
}

function getProductsSynchVariations($id_category, $ebay_profile)
{
    $sql = 'SELECT p.`id_product` as id, pl.`name`, epc.`blacklisted`, epc.`extra_images`, sa.`quantity` as stock
            FROM `'._DB_PREFIX_.'product` p';

    $sql .= Shop::addSqlAssociation('product', 'p');
    $sql .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (p.`id_product` = pl.`id_product`
                AND pl.`id_lang` = '.(int) $ebay_profile->id_lang;
    $sql .= Shop::addSqlRestrictionOnLang('pl');
    $sql .= ')
            LEFT JOIN `'._DB_PREFIX_.'ebay_product_configuration` epc
                ON p.`id_product` = epc.`id_product` AND epc.id_ebay_profile = '.(int)$ebay_profile->id.'
            LEFT JOIN `'._DB_PREFIX_.'stock_available` sa
                ON p.`id_product` = sa.`id_product`
                AND sa.`id_product_attribute` = 0
            WHERE ';
    $sql .= ' product_shop.`id_category_default` = '.(int) $id_category;
    $sql .= StockAvailable::addSqlShopRestriction(null, null, 'sa');


    $nb_products_blocked = 0;
    $nb_products_man = Db::getInstance()->ExecuteS($sql);
    $nb_products_variations = 0;
    if ($nb_products_man) {
        foreach ($nb_products_man as $product_ps) {
            $product = new Product($product_ps['id']);
            $variation = $product->getWsCombinations();
            $nb_products_variations += count($variation);
            if ($product_ps['blacklisted']) {
                $nb_products_blocked += 1;
            }
        }
    }

    return $nb_products_variations;
}

if ($id_categori_ps = Tools::getValue('id_category_ps')) {
    if (Module::isInstalled('ebay')) {
        /** @var Ebay $ebay */
        $ebay = Module::getInstanceByName('ebay');

        $context = Context::getContext();
        $context->shop = new Shop((int) Tools::getValue('id_shop'));
        $ebay_profile = new EbayProfile((int) Tools::getValue('profile'));
        $root_category = Category::getRootCategory();
        $categories = Category::getCategories(Tools::getValue('id_lang'));
        $category_list = $ebay->getChildCategories($categories, $root_category->id_parent, array(), '', Tools::getValue('s'));
        $nb_categories = count($category_list);

        $ebay_category_list = Db::getInstance()->executeS('SELECT *
            FROM `'._DB_PREFIX_.'ebay_category`
            WHERE `id_category_ref` = `id_category_ref_parent`
            AND `id_country` = '.(int) $ebay_profile->ebay_site_id);
        $rq_products = '
                SELECT COUNT(DISTINCT(p.`id_product`)) AS nbProducts,
                    COUNT(DISTINCT(epc.`id_product`)) AS nbNotSyncProducts
                FROM `'._DB_PREFIX_.'product` AS p

                INNER JOIN `'._DB_PREFIX_.'product_shop` AS ps
                ON p.`id_product` = ps.`id_product`

                LEFT JOIN `'._DB_PREFIX_.'ebay_product_configuration` AS epc
                ON p.`id_product` = epc.`id_product`
                AND epc.`id_ebay_profile` = '.(int) $ebay_profile->id.'
                AND epc.blacklisted = 1

                WHERE 1 '.$ebay->addSqlRestrictionOnLang('ps').'
                AND ps.`id_shop` = '.(int)$ebay_profile->id_shop.'
                AND ps.`id_category_default` = '.(int) $id_categori_ps.'
                GROUP BY ps.`id_category_default`';


        $get_products = Db::getInstance()->ExecuteS($rq_products);
        $get_cat_nb_products = array();
        $get_cat_nb_sync_products = array();
        foreach ($get_products as $data) {
            $get_cat_nb_products = (int) $data['nbProducts'];
            $get_cat_nb_sync_products = (int) $data['nbProducts'] - (int) $data['nbNotSyncProducts'];
        }
        /* Loading categories */
        $category_config_list = array();
        /* init refcats */
        $ref_categories = array();
        /* init levels */
        $levels = array();
        /* init selects */
        $sql = '
            SELECT *, ec.`id_ebay_category` AS id_ebay_category
            FROM `'._DB_PREFIX_.'ebay_category` AS ec
            LEFT OUTER JOIN `'._DB_PREFIX_.'ebay_category_configuration` AS ecc
            ON ec.`id_ebay_category` = ecc.`id_ebay_category`
            AND ecc.`id_ebay_profile` = '.(int) $ebay_profile->id.'
            WHERE ec.`id_country` = '.(int) $ebay_profile->ebay_site_id.'
            
            ORDER BY `level`';

        $datas = Db::getInstance()->executeS($sql);
        
        foreach ($datas as $category) {
            /* Add datas */
            if (isset($category['id_category'])) {
                $category_config_list[$category['id_category']] = $category;
            }

            /* add refcats */
            if (!isset($ref_categories[$category['id_category_ref']])) {
                $ref_categories[$category['id_category_ref']] = $category;
                /* Create children in refcats */
                if ($category['id_category_ref'] != $category['id_category_ref_parent']) {
                    if (!isset($ref_categories[$category['id_category_ref_parent']]['children'])) {
                        $ref_categories[$category['id_category_ref_parent']]['children'] = array();
                    }
                    if (!in_array($category['id_category_ref'], $ref_categories[$category['id_category_ref_parent']]['children'])) {
                        $ref_categories[$category['id_category_ref_parent']]['children'][] = $category['id_category_ref'];
                    }
                }
            }
        }
        foreach ($category_config_list as &$category) {
            $tpl_var = array(
                "value" => (int) $category['id_ebay_category']
            );
            $context->smarty->assign($tpl_var);

            $category['var'] = getSelectors($ref_categories, $category['id_category_ref'], $category['id_category'], $category['level'], $ebay).$ebay->display(realpath(dirname(__FILE__).'/../'), '/views/templates/hook/form_select_change_category_match_input.tpl');

            if ($category['percent']) {
                preg_match('#^([-|+]{0,1})([0-9]{0,3}[\.|\,]?[0-9]{0,2})([\%]{0,1})$#is', $category['percent'], $temp);

                $category['percent'] = array('sign' => $temp[1], 'value' => $temp[2], 'type' => ($temp[3]==''?'€':$temp[3]));
            } else {
                $category['percent'] = array('sign' => '+', 'value' => '0', 'type' => '€');
            }
        }
        $smarty = Context::getContext()->smarty;
        $currency = new Currency((int) $ebay_profile->getConfiguration('EBAY_CURRENCY'));
        /* Smarty datas */
        $ebay_store_category_list = EbayStoreCategory::getCategoriesWithConfiguration($ebay_profile->id);
        foreach ($category_list as $category_l) {
            if ($category_l['id_category'] == $id_categori_ps) {
                $ps_category_real = $category_l;
            }
        }

        $bp_policies = EbayBussinesPolicies::getPoliciesConfigurationbyIdCategory($category_config_list[$id_categori_ps]['id_category_ref'], $ebay_profile->id);

        $storeCategoryId = EbayStoreCategoryConfiguration::getEbayStoreCategoryIdByIdProfileAndIdCategory($ebay_profile->id, $id_categori_ps);

        if ($category_config_list[$id_categori_ps]['percent']) {
            $temp = $category_config_list[$id_categori_ps]['percent'];
            //$temp = explode(':', $percent['sign']);
            $percent = array('sign' => $temp['sign'], 'value' => $temp['value'], 'type' => $temp['type']);
        } else {
            $percent = array('sign' => '', 'value' => '', 'type' => '');
        }
        $vars = array(
            'tabHelp' => '&id_tab=7',
            '_path' => $ebay->getPath(),
            'categoryList' => $ps_category_real,
            'nbCategories' => $nb_categories,
            'eBayCategoryList' => $ebay_category_list,
            'getNbProducts' => $get_cat_nb_products,
            'getNbSyncProducts' => $get_cat_nb_sync_products,
            'getNbSyncProductsVariations' => getProductsSynchVariations($id_categori_ps, $ebay_profile),
            'categoryConfigList' => $category_config_list[$id_categori_ps],
            'currencySign' => $currency->sign,
            'percent' => $percent,
            'storeCategoryId' => $storeCategoryId?$storeCategoryId : 0,
            'bp_policies' => ($bp_policies)?$bp_policies[0]:null,
        );

        echo Tools::jsonEncode($vars);
    }
}
