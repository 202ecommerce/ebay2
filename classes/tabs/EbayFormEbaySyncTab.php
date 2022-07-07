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
if (_PS_VERSION_ > '1.7') {
    include_once dirname(__FILE__) . '/../../classes/EbaySynchronizer.php';
}

class EbayFormEbaySyncTab extends EbayTab
{
    public function getContent($page_current = 1, $length = 20, $searche = false, $filter = [])
    {
        $national_shipping = EbayShipping::getNationalShippings($this->ebay_profile->id);
        if (empty($national_shipping)) {
            $vars = [
                'msg' => $this->ebay->l('Please configure the \'National Shipping\' tab before using this tab', 'ebayformebaysynctab'),
            ];

            return $this->display('alert_tabs.tpl', $vars);
        }

        // Display Form
        $url_vars = [
            'id_tab' => '5',
            'section' => 'sync',
        ];

        $url_vars['controller'] = Tools::getValue('controller');

        $action_url = $this->_getUrl($url_vars);
        $rootCategory = Category::getRootCategory($this->context->language->id, $this->context->shop);
        // Loading categories
        $category_config_list = [];
        //var_dump(Category::getCategories($this->context->language->id, true, true, ' and c.id_category=5')); die();
        foreach (EbayCategoryConfiguration::getEbayCategoryConfigurations($this->ebay_profile->id) as $c) {
            $category_config_list[$c['id_category']] = $c;
        }
        if ($searche ||
            (isset($filter['id_product']) && $filter['id_product']) ||
            (isset($filter['name_product']) && $filter['name_product'])
        ) {
            $idsCategories = $this->getIdsCategory($filter);
            if (!empty($idsCategories)) {
                $idsCategories = implode(',', $idsCategories);
                $query_filter = " AND c.id_category IN ($idsCategories)";
            } else {
                $query_filter = ' AND 0';
            }

            $category_list = $this->ebay->getChildCategories(Category::getCategories($this->context->language->id, false, true, '', $query_filter), $rootCategory->id, [], '', $searche);
        } else {
            $category_list = $this->ebay->getChildCategories(Category::getCategories($this->context->language->id, false), $rootCategory->id);
        }

        $categories = [];
        $ids_category = [];
        $pagination = false;
        if ($category_list) {
            $alt_row = false;
            $count_categories = 0;
            foreach ($category_list as $category) {
                if (isset($category_config_list[$category['id_category']]['id_ebay_category'])
                    && $category_config_list[$category['id_category']]['id_ebay_category'] > 0
                ) {
                    $ids_category[$category['id_category']] = $category;
                    ++$count_categories;
                }
            }
            asort($ids_category);
            $pages_all = ceil($count_categories / $length);
            $pagination = $pages_all > 1 ? true : false;
            $range = 3;
            $start = $page_current - $range;
            if ($start <= 0) {
                $start = 1;
            }

            $stop = $page_current + $range;
            if ($stop > $pages_all) {
                $stop = $pages_all;
            }

            $offset = ($page_current - 1) * $length;
            $part_ids_category = array_slice($ids_category, $offset, $length);
            foreach ($part_ids_category as $id_category => $category) {
                $sql = 'SELECT p.`id_product` as id, pl.`name`, epc.`blacklisted`, epc.`extra_images`, sa.`quantity` as stock
            FROM `' . _DB_PREFIX_ . 'product` p';

                $sql .= Shop::addSqlAssociation('product', 'p');
                $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (p.`id_product` = pl.`id_product`
                
                AND pl.`id_lang` = ' . (int) $this->ebay_profile->id_lang;
                $sql .= Shop::addSqlRestrictionOnLang('pl');
                $sql .= ')
            LEFT JOIN `' . _DB_PREFIX_ . 'ebay_product_configuration` epc
                ON p.`id_product` = epc.`id_product` AND epc.id_ebay_profile = ' . (int) $this->ebay_profile->id . '
            LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa
                ON p.`id_product` = sa.`id_product`
                AND sa.`id_product_attribute` = 0
            WHERE  p.`active` = 1 AND ';
                $sql .= ' product_shop.`id_category_default` = ' . (int) $category['id_category'];
                $sql .= StockAvailable::addSqlShopRestriction(null, null, 'sa');

                $nb_products_blocked = 0;
                $nb_products_variations_blocked = 0;
                $nb_products_man = Db::getInstance()->ExecuteS($sql);
                $nb_products_variations = 0;
                $count_variation_by_product = $this->getCountVariationProductByIdCategory((int) $category['id_category']);

                if ($nb_products_man) {
                    foreach ($nb_products_man as $product_ps) {
                        $count_variation = isset($count_variation_by_product[$product_ps['id']]) ? $count_variation_by_product[$product_ps['id']] : 1;
                        $nb_products_variations += $count_variation;
                        if ($product_ps['blacklisted']) {
                            ++$nb_products_blocked;
                            $nb_products_variations_blocked += $count_variation;
                        }
                    }
                }

                $category_ebay = EbayCategoryConfiguration::getEbayCategoryById($this->ebay_profile->id, $category_config_list[$category['id_category']]['id_ebay_category']);
                // $is_multi = EbayCategory::getInheritedIsMultiSku($category_config_list[$category['id_category']]['id_ebay_category'], $this->ebay_profile->ebay_site_id);
                $ebay_category = EbaySynchronizer::__getEbayCategory($category['id_category'], $this->ebay_profile);
                if ($category_config_list[$category['id_category']]['percent']) {
                    preg_match('#^([-|+]{0,1})([0-9]{0,3}[\.|\,]?[0-9]{0,3})([\%]{0,1})$#is', $category_config_list[$category['id_category']]['percent'], $temp);
                    $prix = ['sign' => $temp[1], 'value' => $temp[2], 'type' => ($temp[3] == '' ? '€' : $temp[3])];
                } else {
                    $prix = ['sign' => '', 'value' => '', 'type' => ''];
                }

                $categories[] = [
                    'row_class' => $alt_row ? 'alt_row' : '',
                    'value' => $category['id_category'],
                    'checked' => ($category_config_list[$category['id_category']]['sync'] == 1 ? 'checked="checked"' : ''),
                    'name' => $category['name'],
                    'price' => $prix['sign'] . $prix['value'] . $prix['type'],
                    'category_ebay' => $category_ebay[0]['name'],
                    'category_multi' => $ebay_category->isMultiSku() ? true : false,
                    'annonces' => EbayProduct::getNbProductsByCategory($this->ebay_profile->id, $category['id_category']),
                    'nb_products' => count($nb_products_man),
                    'nb_products_variations' => $nb_products_variations,
                    'nb_products_blocked' => $nb_products_blocked,
                    'nb_variations_tosync' => $nb_products_variations - $nb_products_variations_blocked,
                    'nb_product_tosync' => count($nb_products_man) - $nb_products_blocked,
                ];
                $alt_row = !$alt_row;
            }
        }

        $ebay_category_list = Db::getInstance()->executeS('SELECT *
            FROM `' . _DB_PREFIX_ . 'ebay_category`
            WHERE `id_category_ref` = `id_category_ref_parent`
            AND `id_country` = ' . (int) $this->ebay_profile->ebay_site_id);

        $tpl_include = _PS_MODULE_DIR_ . 'ebay/views/templates/hook/pagination.tpl';

        $smarty_vars = [
            'filter' => $filter,
            'searche' => $searche,
            'prev_page' => isset($page_current) ? $page_current - 1 : 0,
            'next_page' => isset($page_current) ? $page_current + 1 : 0,
            'tpl_include' => $tpl_include,
            'pagination' => $pagination,
            'pages_all' => isset($pages_all) ? $pages_all : 0,
            'page_current' => isset($page_current) ? $page_current : 0,
            'start' => isset($start) ? $start : 0,
            'stop' => isset($stop) ? $stop : 0,
            'category_alerts' => $this->_getAlertCategories($categories),
            'path' => $this->path,
            'nb_products' => 0,
            'nb_products_mode_a' => 0,
            'nb_products_mode_b' => 0,

            'sync_message_exit' => $this->ebay->l('A synchronization is currently underway. If you leave this page, it will be abandoned.', 'ebayformebaysynctab'),
            'action_url' => $action_url,
            'ebay_sync_option_resync' => $this->ebay_profile->getConfiguration('EBAY_SYNC_OPTION_RESYNC'),
            'categories' => $categories,
            'sync_1' => (Tools::getValue('section') == 'sync' && Tools::getValue('ebay_sync_mode') == '1' && Tools::getValue('btnSubmitSyncAndPublish')),
            'sync_2' => (Tools::getValue('section') == 'sync' && Tools::getValue('ebay_sync_mode') == '2' && Tools::getValue('btnSubmitSyncAndPublish')),
            'is_sync_mode_b' => ($this->ebay_profile->getConfiguration('EBAY_SYNC_PRODUCTS_MODE') == 'B'),
            'ebay_sync_mode' => (int) ($this->ebay_profile->getConfiguration('EBAY_SYNC_MODE') ? $this->ebay_profile->getConfiguration('EBAY_SYNC_MODE') : 2),
            'PAYEMENTS' => EbayBussinesPolicies::getPoliciesbyType('PAYMENT', $this->ebay_profile->id),
            'RETURN_POLICY' => EbayBussinesPolicies::getPoliciesbyType('RETURN_POLICY', $this->ebay_profile->id),
            'storeCategories' => EbayStoreCategory::getCategoriesWithConfiguration($this->ebay_profile->id),
            'ebayCategories' => $ebay_category_list,
            //'ps_categories' => $category_list,
            'conditions' => $this->_translatePSConditions(EbayCategoryConditionConfiguration::getPSConditions()),
            'possible_attributes' => AttributeGroup::getAttributesGroups($this->context->cookie->id_lang),
            'possible_features' => Feature::getFeatures($this->context->cookie->id_lang, true),
            'id_lang' => $this->ebay_profile->id_lang,
            'id_employee' => isset($this->context->employee) && $this->context->employee->id > 0 ? $this->context->employee->id : 0,
            'date' => pSQL(date('Ymdhis')),
            'shipping_tab_is_conf' => (empty($national_shipping) ? 1 : 0),
            'bp_active' => ($this->ebay_profile->getConfiguration('EBAY_BUSINESS_POLICIES')) ? $this->ebay_profile->getConfiguration('EBAY_BUSINESS_POLICIES') : 0,
        ];

        return $this->display('formEbaySync.tpl', $smarty_vars);
    }

    public function postProcess()
    {
        // Update Sync Option
        if ($this->ebay_profile == null) {
            return;
        }

        $this->ebay_profile->setConfiguration('EBAY_SYNC_OPTION_RESYNC', (Tools::getValue('ebay_sync_option_resync') == 1 ? 1 : 0));

        // Empty error result
        $this->ebay_profile->setConfiguration('EBAY_SYNC_LAST_PRODUCT', 0);

        if (file_exists(dirname(__FILE__) . '/../../log/syncError.php')) {
            @unlink(dirname(__FILE__) . '/../../log/syncError.php');
        }

        $this->ebay_profile->setConfiguration('EBAY_SYNC_MODE', Tools::getValue('ebay_sync_mode'));

        if (Tools::getValue('ebay_sync_products_mode') == 'A') {
            $this->ebay_profile->setConfiguration('EBAY_SYNC_PRODUCTS_MODE', 'A');
        } else {
            $this->ebay_profile->setConfiguration('EBAY_SYNC_PRODUCTS_MODE', 'B');

            // Select the sync Categories and Retrieve product list for eBay (which have matched and sync categories)
            if (Tools::getValue('category')) {
                EbayCategoryConfiguration::updateByIdProfile($this->ebay_profile->id, ['sync' => 0]);
                foreach (Tools::getValue('category') as $id_category) {
                    EbayCategoryConfiguration::updateByIdProfileAndIdCategory((int) $this->ebay_profile->id, $id_category, ['id_ebay_profile' => $this->ebay_profile->id, 'sync' => 1]);
                }
            }
        }
    }

    /*
     *
     * Get alert to see if some multi variation product on PrestaShop were added to a non multi sku categorie on ebay
     *
     */
    private function _getAlertCategories($categories)
    {
        $alert = '';
        if (empty($categories)) {
            return $alert;
        }

        $cat_with_problem = '';
        foreach ($categories as $cat) {
            if (!$cat['category_multi'] && $cat['nb_products_variations'] > $cat['nb_products']) {
                $cat_with_problem = $cat['name'];
                break;
            }
        }

        if ($cat_with_problem != '') {
            $alert = $this->ebay->l('You have chosen eBay category : ', 'ebayformebaysynctab') . ' "' . $cat_with_problem . '" ' . $this->ebay->l(' which does not support multivariation products. Each variation of a product will generate a new product in eBay', 'ebayformebaysynctab');
        }

        return $alert;
    }

    private function _translatePSConditions($ps_conditions)
    {
        foreach ($ps_conditions as &$condition) {
            switch ($condition) {
                case 'new':
                    $condition = $this->ebay->l('new');
                    break;
                case 'used':
                    $condition = $this->ebay->l('used');
                    break;
                case 'refurbished':
                    $condition = $this->ebay->l('refurbished');
                    break;
            }
        }

        return $ps_conditions;
    }

    //$filter array that contains keys for filter
    //return array that contains ids categories

    public function getIdsCategory($filter)
    {
        $id_lang = $this->context->language->id;
        $query = 'SELECT DISTINCT (c.id_category) FROM ' . _DB_PREFIX_ . 'category c
                  LEFT JOIN ' . _DB_PREFIX_ . 'product p ON c.id_category=p.id_category_default 
                  LEFT JOIN ' . _DB_PREFIX_ . "product_lang pl ON pl.id_product=p.id_product AND pl.id_lang=$id_lang WHERE 1 ";
        if ($filter['id_product']) {
            $id_product = pSQL($filter['id_product']);
            $query .= ' AND p.id_product=' . (int) $id_product;
        }
        if ($filter['name_product']) {
            $name_product = pSQL($filter['name_product']);
            $query .= " AND pl.name LIKE '%$name_product%'";
        }

        $result = DB::getInstance()->ExecuteS($query);
        $ids_categories = [];
        if ($result) {
            foreach ($result as $row) {
                $ids_categories[] = $row['id_category'];
            }
        }

        return $ids_categories;
    }

    public function getCountVariationProductByIdCategory($id_category)
    {
        $return = [];
        $query = new DBQuery();
        $query->select('ps.id_product, COUNT(ps.id_product) as count_variation');
        $query->from('product_shop', 'ps');
        $query->leftJoin('product_attribute_shop', 'pas', 'ps.id_product = pas.id_product AND ps.id_shop = pas.id_shop');
        $query->where('ps.id_shop = ' . $this->context->shop->id);
        $query->where('ps.id_category_default = ' . $id_category);
        $query->groupBy('ps.id_product');

        $result_query = DB::getInstance()->executeS($query);
        if (!$result_query) {
            return $return;
        }

        foreach ($result_query as $row) {
            $return[$row['id_product']] = (int) $row['count_variation'];
        }

        return $return;
    }
}
