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

class AdminFormEbaySyncController extends ModuleAdminController
{

    public function ajaxProcessDeleteConfigCategory()
    {
        $sql = (new DbQuery())
            ->select('id_product')
            ->from('product_shop')
            ->where('id_category_default = ' . (int)Tools::getValue('ebay_category'))
            ->where('id_shop = ' . Context::getContext()->shop->id);
        $to_synchronize_product_ids = Db::getInstance()->ExecuteS($sql);

        foreach ($to_synchronize_product_ids as $product_id_to_sync) {
            EbayTaskManager::deleteTaskForPorductAndEbayProfile($product_id_to_sync['id_product'], Tools::getValue('profile'));
        }

        EbayCategoryConfiguration::deleteByIdCategory(Tools::getValue('profile'), Tools::getValue('ebay_category'));
    }

    public function ajaxProcessResetBp()
    {
        require_once dirname(__FILE__).'/../../classes/EbayProfile.php';
        require_once dirname(__FILE__).'/../../classes/EbayRequest.php';
        if (Tools::getValue('admin_path')) {
            define('_PS_ADMIN_DIR_', realpath(dirname(__FILE__).TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'..'.TMP_DS).TMP_DS.EbayTools::getValue('admin_path').TMP_DS);
        }

        $ebay_profile = new EbayProfile((int) Tools::getValue('profile'));

        $request = new EbayRequest($ebay_profile->id);
        $request->importBusinessPolicies();
        $vars = array(
            'PAYEMENTS' =>EbayBussinesPolicies::getPoliciesbyType('PAYMENT', $ebay_profile->id),
            'RETURN_POLICY' => EbayBussinesPolicies::getPoliciesbyType('RETURN_POLICY', $ebay_profile->id),
        );

// Smarty datas
        $context = Context::getContext();
        $ebay = new Ebay();
        $context->smarty->assign($vars);

        echo $ebay->display(realpath(dirname(__FILE__).'/../../'), '/views/templates/hook/bp_selects.tpl');
    }

    public function ajaxProcessSaveConfigFormCategory()
    {
        $id_ebay_profile = Tools::getValue('profile');
        $ebay_profile = new EbayProfile($id_ebay_profile);
        $ebay_category = Tools::getValue('real_category_ebay');
// Insert and update categories
        if ($ebay_category) {
            $percent = array();
            $ps_categories = explode(",", Tools::getValue('ps_categories'));

            $impact_prix = Tools::getValue('impact_prix');
            $percent_sign_type = $impact_prix['sign'];
            $percent['value'] = $impact_prix['value'];
            $percent['type'] = $impact_prix['type'];
            foreach ($ps_categories as $id_category) {
                $data = array();
                $date = date('Y-m-d H:i:s');
                if ($percent['value'] != '') {
                    //$percent_sign_type = explode(':', $percent['sign']);
                    $percentValue = ($percent_sign_type == '-' ? $percent_sign_type : '+').round($percent['value'], 2).($percent['type'] == 'percent' ? '%' : '');
                } else {
                    $percentValue = null;
                }

                $data = array(
                    'id_ebay_profile' => (int) $id_ebay_profile,
                    'id_country' => (int) $ebay_profile->ebay_site_id,
                    'id_ebay_category' => (int) $ebay_category,
                    'id_category' => (int) $id_category,
                    'percent' => pSQL($percentValue),
                    'date_upd' => pSQL($date),
                    'sync' => 1,
                );
                if (EbayCategoryConfiguration::getIdByCategoryId($id_ebay_profile, $id_category)) {
                    if ($data) {
                        EbayCategoryConfiguration::updateByIdProfileAndIdCategory($id_ebay_profile, $id_category, $data);
                    } else {
                        EbayCategoryConfiguration::deleteByIdCategory($id_ebay_profile, $id_category);
                    }
                } elseif ($data) {
                    $data['date_add'] = $date;
                    EbayCategoryConfiguration::add($data);
                }
            }
            // make sur the ItemSpecifics and Condition data are refresh when we load the dedicated config screen the next time
            $ebay_profile->deleteConfigurationByName('EBAY_SPECIFICS_LAST_UPDATE');
        }

// update extra_images for all products
        if (($all_nb_extra_images = Tools::getValue('all-extra-images-value', -1)) != -1) {
            $product_ids = EbayCategoryConfiguration::getAllProductIds($ebay_profile->id);

            foreach ($product_ids as $product_id) {
                EbayProductConfiguration::insertOrUpdate($product_id, array(
                    'extra_images' => $all_nb_extra_images ? $all_nb_extra_images : 0,
                    'id_ebay_profile' => $ebay_profile->id,
                ));
            }
        }

// update products configuration
        if (is_array(Tools::getValue('showed_products'))) {
            $showed_product_ids = array_keys(Tools::getValue('showed_products'));

            if (Tools::getValue('to_synchronize')) {
                $to_synchronize_product_ids = array_keys(Tools::getValue('to_synchronize'));
            } else {
                $to_synchronize_product_ids = array();
            }

            foreach ($showed_product_ids as $product_id) {
                EbayProductConfiguration::insertOrUpdate($product_id, array(
                    'id_ebay_profile' => $id_ebay_profile,
                    'blacklisted' => in_array($product_id, $to_synchronize_product_ids) ? 0 : 1,
                    'extra_images' => 0,
                ));
            }

            foreach ($to_synchronize_product_ids as $product_id_to_sync) {
                $product = new Product($product_id_to_sync);
                EbayTaskManager::addTask('add', $product, Tools::getValue('id_employee'), $id_ebay_profile);
            }
            // TODO remove extra_image
        }

        if (Tools::getValue('payement_policies') || Tools::getValue('return_policies')) {
            $payment = Tools::getValue('payement_policies');
            $return = Tools::getValue('return_policies');
            $ebay_category_id_ref = EbayCategory::getIdCategoryRefById($ebay_category, $ebay_profile->ebay_site_id);
            $data = array(
                'id_ebay_profile' => (int) $id_ebay_profile,
                'id_category' => (int) $ebay_category_id_ref,
                'id_return' => pSQl($return),
                'id_payment' => pSQL($payment),
            );

            EbayBussinesPolicies::deletePoliciesConfgbyidCategories($id_ebay_profile, $ebay_category_id_ref);

            Db::getInstance()->insert('ebay_category_business_config', $data);

            $ebay_profile->setConfiguration('EBAY_BUSINESS_POLICIES_CONFIG', 1);
        }

        if (Tools::getValue('specific')) {
            foreach (Tools::getValue('specific') as $specific_id => $data) {
                if ($data) {
                    list($data_type, $value) = explode('-', $data);
                } else {
                    $data_type = null;
                }

                $field_names = EbayCategorySpecific::getPrefixToFieldNames();
                $data = array_combine(array_values($field_names), array(null, null, null, null, null, null, null));

                if ($data_type) {
                    $data[$field_names[$data_type]] = pSQL($value);
                }

                Db::getInstance()->update('ebay_category_specific', $data, 'id_ebay_category_specific = '.(int) $specific_id);
            }
        }

// save conditions
        if (Tools::getValue('condition')) {
            foreach (Tools::getValue('condition') as $category_id => $condition) {
                $ebay_category_id_ref = EbayCategory::getIdCategoryRefById($category_id, $ebay_profile->ebay_site_id);
                foreach ($condition as $type => $condition_ref) {
                    EbayCategoryConditionConfiguration::replace(array('id_ebay_profile' => $id_ebay_profile, 'id_condition_ref' => $condition_ref, 'id_category_ref' => $ebay_category_id_ref, 'condition_type' => $type));
                }
            }
        }

// Insert and update categories
        if ($store_categories = Tools::getValue('store_category')) {
            // insert rows
            foreach (explode(',', Tools::getValue('ps_categories')) as $id_category) {
                EbayStoreCategoryConfiguration::update($id_ebay_profile, $store_categories, (int)$id_category);
            }
        }



        if (Tools::getValue('ajax')) {
            die('{"valid" : true}');
        }
    }

    public function ajaxProcessActiveCategory()
    {
        require_once dirname(__FILE__).'/../../ebay.php';

        $category_conf = EbayCategoryConfiguration::getConfigByCategoryId(Tools::getValue('profile'), Tools::getValue('ebay_category'));

        if ($category_conf[0]['sync']) {
            $value =0;
        } else {
            $value =1;
        }

        EbayCategoryConfiguration::updateByIdProfileAndIdCategory(Tools::getValue('profile'), Tools::getValue('ebay_category'), $data = array('sync' => $value));

        $sql = 'SELECT p.`id_product`
            FROM `'._DB_PREFIX_.'product` p';

        $sql .= Shop::addSqlAssociation('product', 'p');
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (p.`id_product` = pl.`id_product`
                AND pl.`id_lang` = '.(int) Tools::getValue('ebay_category');
        $sql .= Shop::addSqlRestrictionOnLang('pl');
        $sql .= ')
            LEFT JOIN `'._DB_PREFIX_.'ebay_product_configuration` epc
                ON p.`id_product` = epc.`id_product` AND epc.id_ebay_profile = '.(int)Tools::getValue('profile').'
            LEFT JOIN `'._DB_PREFIX_.'stock_available` sa
                ON p.`id_product` = sa.`id_product`
                AND sa.`id_product_attribute` = 0
            WHERE ';
        $sql .= ' product_shop.`id_category_default` = '.(int) Tools::getValue('ebay_category');
        $sql .= StockAvailable::addSqlShopRestriction(null, null, 'sa');

        $to_synchronize_product_ids = Db::getInstance()->ExecuteS($sql);

        if ($value) {
            foreach ($to_synchronize_product_ids as $product_id_to_sync) {
                if (!EbayProductConfiguration::isblocked(Tools::getValue('profile'), $product_id_to_sync['id_product'])) {
                    $product = new Product($product_id_to_sync['id_product']);
                    EbayTaskManager::addTask('add', $product, Tools::getValue('id_employee'), Tools::getValue('profile'));
                }
            }
        } else {
            foreach ($to_synchronize_product_ids as $product_id_to_sync) {
                EbayTaskManager::deleteTaskForPorductAndEbayProfile($product_id_to_sync['id_product'], Tools::getValue('profile'));
            }
        }
        $count_orphans_product = EbayProduct::getCountOrphanListing(Tools::getValue('profile'));
        echo $count_orphans_product[0]['number'];
    }

    public function getSelectors($ref_categories, $id_category_ref, $id_category, $level, $ebay)
    {
        $var = null;
        $context = Context::getContext();
        $ebay = new Ebay();

        if ((int)$level > 1) {
            foreach ($ref_categories as $ref_id_category_ref => $category) {
                if ($ref_id_category_ref == $id_category_ref) {
                    if (isset($ref_categories[$category['id_category_ref_parent']]['children'])) {
                        if ((int) $category['id_category_ref'] != (int) $category['id_category_ref_parent']) {
                            $var .= $this->getSelectors($ref_categories, (int) $category['id_category_ref_parent'], (int) $id_category, (int) ($level - 1), $ebay);
                        }
                        $tpl_var = array(
                            "level" => $level,
                            "id_category" => $id_category,
                            "category" => $category,
                            "ref_categories" => $ref_categories,
                        );
                        $context->smarty->assign($tpl_var);
                        $var .=  $ebay->display(realpath(dirname(__FILE__).'/../../'), '/views/templates/hook/form_select_categories.tpl');
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
            $var .=  $ebay->display(realpath(dirname(__FILE__).'/../../'), '/views/templates/hook/form_select_categories.tpl');
        }

        return $var;
    }

    public function getProductsSynchVariations($id_category, $ebay_profile)
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

    public function ajaxProcessLoadConfigFromCategory()
    {
        if ($id_categori_ps = Tools::getValue('id_category_ps')) {
            if (Module::isInstalled('ebay')) {
                /** @var Ebay $ebay */
                $ebay = Module::getInstanceByName('ebay');

                $context = Context::getContext();
                $context->shop = new Shop((int) Tools::getValue('id_shop'));
                $ebay_profile = new EbayProfile((int) Tools::getValue('profile'));
                $root_category = Category::getRootCategory();
                $categories = Category::getCategories(Tools::getValue('id_lang'));
                $category_list = $ebay->getChildCategories($categories, $root_category->id, array(), '', Tools::getValue('s'));
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
            AND ecc.`id_category` = '.(int) $id_categori_ps.'
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

                    $category['var'] = $this->getSelectors($ref_categories, $category['id_category_ref'], $category['id_category'], $category['level'], $ebay).$ebay->display(realpath(dirname(__FILE__).'/../../'), '/views/templates/hook/form_select_change_category_match_input.tpl');

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
                    'getNbSyncProductsVariations' => $this->getProductsSynchVariations($id_categori_ps, $ebay_profile),
                    'categoryConfigList' => $category_config_list[$id_categori_ps],
                    'currencySign' => $currency->sign,
                    'percent' => $percent,
                    'storeCategoryId' => $storeCategoryId?$storeCategoryId : 0,
                    'bp_policies' => ($bp_policies)?$bp_policies[0]:null,
                );

                die(Tools::jsonEncode($vars));
            }
        }
    }

    public function loadItemsMap($row)
    {
        return $row['id'];
    }

    public function ajaxProcessLoadItemsSpecificsAndConditions()
    {
        require_once dirname(__FILE__).'/../../classes/EbayCategorySpecific.php';
        require_once dirname(__FILE__).'/../../classes/EbayCategoryCondition.php';

        $id_ebay_profile = (int) Tools::getValue('profile');
        $ebay_profile = new EbayProfile($id_ebay_profile);


        /* Fix for limit db sql request in time */
        sleep(1);
        $sql = 'SELECT `id_category_ref` FROM `'._DB_PREFIX_.'ebay_category` WHERE `id_ebay_category` = '.(int) Tools::getValue('ebay_category');
        $id_category = DB::getInstance()->getValue($sql);
        $category = new EbayCategory($ebay_profile, (int) $id_category);

        $last_upd = $ebay_profile->getConfiguration('EBAY_SPECIFICS_LAST_UPDATE');

        $update = false;

        if (Tools::jsonDecode($last_upd) === null) {
            $last_update = array();
            $update = true;
        } else {
            $last_update = get_object_vars(Tools::jsonDecode($last_upd));

            if (!isset($last_update[$category->getIdCategoryRef()])
                || ($last_update[$category->getIdCategoryRef()] < date('Y-m-d\TH:i:s', strtotime('-3 days')).'.000Z')) {
                $update = true;
            }
        }

        if ($update) {
            $time = time();
            $res = EbayCategorySpecific::loadCategorySpecifics($id_ebay_profile, $category->getIdCategoryRef());
            $res &= EbayCategoryCondition::loadCategoryConditions($id_ebay_profile, $category->getIdCategoryRef());

            if ($res) {
                $last_update[$category->getIdCategoryRef()] = date('Y-m-d\TH:i:s.000\Z');
                $ebay_profile->setConfiguration('EBAY_SPECIFICS_LAST_UPDATE', Tools::jsonEncode($last_update), false);
            }
        }

        $item_specifics = $category->getItemsSpecifics();
        $item_specifics_ids = array();
        foreach ($item_specifics as $item_specific) {
            $item_specifics_ids[] = $item_specific['id'];
        }
       // $item_specifics_ids = array_map('loadItemsMap', $item_specifics);

        if (count($item_specifics_ids)) {
            $sql = 'SELECT `id_ebay_category_specific_value` as id, `id_ebay_category_specific` as specific_id, `value`
        FROM `'._DB_PREFIX_.'ebay_category_specific_value`
        WHERE `id_ebay_category_specific` in ('.implode(',', $item_specifics_ids).')';

            $item_specifics_values = DB::getInstance()->executeS($sql);
        } else {
            $item_specifics_values = array();
        }

        foreach ($item_specifics as &$item_specific) {
            foreach ($item_specifics_values as $value) {
                if ($item_specific['id'] == $value['specific_id']) {
                    $item_specific['values'][$value['id']] = Tools::safeOutput($value['value']);
                }
            }
        }

        $respons = Tools::jsonEncode(array(
            'specifics' => $item_specifics,
            'conditions' => $category->getConditionsWithConfiguration($id_ebay_profile),
            'is_multi_sku' => $category->isMultiSku(),
        ));
        die($respons);
    }

    public function ajaxProcessGetProducts()
    {
        include_once dirname(__FILE__).'/../../classes/EbayCountrySpec.php';
        include_once dirname(__FILE__).'/../../classes/EbayProductConfiguration.php';

        $ebay_country = EbayCountrySpec::getInstanceByKey(Configuration::get('EBAY_COUNTRY_DEFAULT'));
        $id_lang = $ebay_country->getIdLang();
        $id_ebay_profile = (int) Tools::getValue('id_ebay_profile');

        $sql = 'SELECT p.`id_product` as id, pl.`name`, epc.`blacklisted`, epc.`extra_images`, sa.`quantity` as stock
            FROM `'._DB_PREFIX_.'product` p';

        $sql .= Shop::addSqlAssociation('product', 'p');
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (p.`id_product` = pl.`id_product`
                AND pl.`id_lang` = '.(int) $id_lang;
        $sql .= Shop::addSqlRestrictionOnLang('pl');
        $sql .= ')
            LEFT JOIN `'._DB_PREFIX_.'ebay_product_configuration` epc
                ON p.`id_product` = epc.`id_product` AND epc.id_ebay_profile = '.(int)$id_ebay_profile.'
            LEFT JOIN `'._DB_PREFIX_.'stock_available` sa
                ON p.`id_product` = sa.`id_product`
                AND sa.`id_product_attribute` = 0
            WHERE product_shop.active = 1 AND ';
        $sql .= ' product_shop.`id_category_default` = '.(int) Tools::getValue('category');
        $sql .= StockAvailable::addSqlShopRestriction(null, null, 'sa');



        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as &$row) {
            $product = new Product($row['id']);
            $variation = $product->getWsCombinations();
            $row['nb_variation'] = count($variation);
            $row['name'] = Tools::safeOutput($row['name']);
            $row['blacklisted'] = Tools::safeOutput($row['blacklisted']);
            $row['extra_images'] = Tools::safeOutput($row['extra_images']);
            $row['stock'] = Tools::safeOutput($row['stock']);
        }

        die(Tools::jsonEncode($res));
    }

    public function ajaxProcessChangeCategoryMatch()
    {
        require_once dirname(__FILE__).'/../../ebay.php';

        $id_ebay_profile = (int) Tools::getValue('profile');
        $ebay_profile = new EbayProfile($id_ebay_profile);

        $levelExists = array();
        $context = Context::getContext();
        $ebay = new Ebay();
        $respons = '';
        for ($level = 0; $level <= 5; $level++) {
            if (Tools::getValue('level') >= $level) {
                if ($level == 0) {
                    $ebay_category_list_level = Db::getInstance()->executeS('SELECT *
                FROM `'._DB_PREFIX_.'ebay_category`
                WHERE `level` = 1
                AND `id_category_ref` = `id_category_ref_parent`
                AND `id_country` = '.(int) $ebay_profile->ebay_site_id);
                } else {
                    $ebay_category_list_level = Db::getInstance()->executeS('SELECT *
                FROM `'._DB_PREFIX_.'ebay_category`
                WHERE `level` = '.(int) ($level + 1).'
                AND `id_country` = '.(int) $ebay_profile->ebay_site_id.'
                AND `id_category_ref_parent`
                IN (
                    SELECT `id_category_ref`
                    FROM `'._DB_PREFIX_.'ebay_category`
                    WHERE `id_ebay_category` = '.(int) (Tools::getValue('level'.$level)).')');
                }

                if ($ebay_category_list_level) {
                    $levelExists[$level + 1] = true;
                    $tpl_var = array(
                        "level" => $level,
                        "id_category" => (int) Tools::getValue('id_category'),
                        "ch_cat_str" => Tools::safeOutput(Tools::getValue('ch_cat_str')),
                        "ebay_category_list_level" => $ebay_category_list_level,
                        "select" => Tools::getValue('level'.($level + 1)),
                    );
                    $context->smarty->assign($tpl_var);
                    $respons .= $ebay->display(realpath(dirname(__FILE__).'/../../'), '/views/templates/hook/form_select_change_category_match.tpl');
                }
            }
        }

        if (!isset($levelExists[(int)Tools::getValue('level') + 1])) {
            $tpl_var = array(
                "value" => (int) Tools::getValue('level'.Tools::getValue('level'))
            );
            $context->smarty->assign($tpl_var);
            $respons .= $ebay->display(realpath(dirname(__FILE__).'/../../'), '/views/templates/hook/form_select_change_category_match_input.tpl');
        }
        die($respons);
    }

    public function ajaxProcessLoadAjaxCategories()
    {
        $ebay = Module::getInstanceByName('ebay');
        $root_category = Category::getRootCategory();
        $categories = Category::getCategories(Tools::getValue('id_lang'));
        $category_list = $ebay->getChildCategories($categories, $root_category->id, array(), '', Tools::getValue('s'));

        $vars = array(
            'categoryList' => $category_list,
        );

        die(Tools::jsonEncode($vars));
    }

    public function ajaxProcessLoadFormSyncTab()
    {
        $page_current = Tools::getValue('page') ? Tools::getValue('page') : 1;
        $length = Tools::getValue('length') ? Tools::getValue('length') : 20;
        $searche = Tools::getValue('searche');
        $filter = array(
            'id_product' => Tools::getValue('id_product'),
            'name_product' => Tools::getValue('name_product'),
        );

        $ebay = Module::getInstanceByName('ebay');
        $context = Context::getContext();
        $form_ebay_sync_tab = new EbayFormEbaySyncTab($ebay, $context->smarty, $context);
        die($form_ebay_sync_tab->getContent((int) $page_current, $length, $searche, $filter));
    }

    public function ajaxProcessButtonResyncProducts()
    {
        if (Tools::getValue('modeResync') == 'resyncProductsAndImages') {
            EbayProductImage::removeAllProductImage();
        }

        $ebay_profile = new EbayProfile(Tools::getValue('id_ebay_profile'));
        $products = EbayProduct::getProductsIdForSync($ebay_profile->id);
        while ($product_id = $products->fetch(PDO::FETCH_ASSOC)) {
            $product = new Product($product_id['id_product'], false, $ebay_profile->id_lang);
            EbayTaskManager::deleteTaskForPorductAndEbayProfile($product_id['id_product'], $ebay_profile->id);
            EbayTaskManager::addTask('update', $product, null, $ebay_profile->id);
        }
        die();
    }
}
