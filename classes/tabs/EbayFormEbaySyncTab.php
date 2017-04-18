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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class EbayFormEbaySyncTab extends EbayTab
{
    public function getContent()
    {
        // Check if the module is configured
        if (!$this->ebay_profile->getConfiguration('EBAY_PAYPAL_EMAIL')) {
            return '<p class="error"><b>' . $this->ebay->l('Please configure the \'General settings\' tab before using this tab', 'ebayformebaysynctab') . '</b></p><br /><script type="text/javascript">$("#menuTab5").addClass("wrong")</script>';
        }

        if (!EbayCategoryConfiguration::getTotalCategoryConfigurations($this->ebay_profile->id)) {
            return '<p class="error"><b>' . $this->ebay->l('Please configure the \'Category settings\' tab before using this tab', 'ebayformebaysynctab') . '</b></p><br /><script type="text/javascript">$("#menuTab5").addClass("wrong")</script>';
        }

        $nb_products_mode_a = EbaySynchronizer::getNbProductsSync($this->ebay_profile->id, "A");
        $nb_products_mode_b = EbaySynchronizer::getNbProductsSync($this->ebay_profile->id, "B");

        $nb_products = ($this->ebay_profile->getConfiguration('EBAY_SYNC_PRODUCTS_MODE') == 'B' ? $nb_products_mode_b : $nb_products_mode_a);

        // Display Form
        $url_vars = array(
            'id_tab'  => '5',
            'section' => 'sync'
        );

        $url_vars['controller'] = Tools::getValue('controller');


        $action_url = $this->_getUrl($url_vars);

        // Loading categories
        $category_config_list = array();

        foreach (EbayCategoryConfiguration::getEbayCategoryConfigurations($this->ebay_profile->id) as $c) {
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
                ON p.`id_product` = epc.`id_product` AND epc.id_ebay_profile = '.(int)$this->ebay_profile->id.'
            LEFT JOIN `'._DB_PREFIX_.'stock_available` sa
                ON p.`id_product` = sa.`id_product`
                AND sa.`id_product_attribute` = 0
            WHERE ';
                    $sql .= ' product_shop.`id_category_default` = '.(int) $category['id_category'];
                    $sql .= StockAvailable::addSqlShopRestriction(null, null, 'sa');


                    $nb_products_blocked = 0;
                    $nb_products_man = Db::getInstance()->ExecuteS($sql);
                    $nb_products_variations = 0;
                    if ($nb_products) {
                        foreach ($nb_products_man as $product_ps) {
                            $product = new Product($product_ps['id']);
                            $variation = $product->getWsCombinations();
                            $nb_products_variations += count($variation);
                            if ($product_ps['blacklisted']) {
                                $nb_products_blocked += 1;
                            }
                        }
                    }

                    $category_ebay =  EbayCategoryConfiguration::getEbayCategoryById($this->ebay_profile->id,$category_config_list[$category['id_category']]['id_ebay_category']);
                   // $is_multi = EbayCategory::getInheritedIsMultiSku($category_config_list[$category['id_category']]['id_ebay_category'], $this->ebay_profile->ebay_site_id);
                    $ebay_category = EbaySynchronizer::__getEbayCategory($category['id_category'], $this->ebay_profile);
                    $id_category_boutique =EbayStoreCategoryConfiguration::getEbayStoreCategoryIdByIdProfileAndIdCategory($this->ebay_profile->id, $category['id_category']);

                    $categories[] = array(
                        'row_class' => $alt_row ? 'alt_row' : '',
                        'value'     => $category['id_category'],
                        'checked'   => ($category_config_list[$category['id_category']]['sync'] == 1 ? 'checked="checked"' : ''),
                        'name'      => $category['name'],
                        'price' => $category_config_list[$category['id_category']]['percent']?$category_config_list[$category['id_category']]['percent']:0,
                        'category_ebay' => $category_ebay[0]['name'],
                        'category_multi' => $ebay_category->isMultiSku()?'yes' : 'non',
                        'annonces' => EbayProduct::getNbProductsByCategory($this->ebay_profile->id, $category['id_category']),
                        'nb_products' => count($nb_products_man),
                        'nb_products_variations' => $nb_products_variations,
                        'nb_products_blocked' => $nb_products_blocked,
                        'category_boutique' => EbayStoreCategory::getCategoryName($id_category_boutique),
                    );
                    $alt_row = !$alt_row;
                }
            }
        }
        $nb_products_sync_url = _MODULE_DIR_.'ebay/ajax/getNbProductsSync.php?token='.Configuration::get('EBAY_SECURITY_TOKEN').'&time='.pSQL(date('Ymdhis')).'&profile='.$this->ebay_profile->id;
        $sync_products_url = _MODULE_DIR_.'ebay/ajax/eBaySyncProduct.php?token='.Configuration::get('EBAY_SECURITY_TOKEN').'&option=\'+option+\'&profile='.$this->ebay_profile->id.'&admin_path='.basename(_PS_ADMIN_DIR_).'&time='.pSQL(date('Ymdhis'));
        $ebay_category_list = Db::getInstance()->executeS('SELECT *
            FROM `'._DB_PREFIX_.'ebay_category`
            WHERE `id_category_ref` = `id_category_ref_parent`
            AND `id_country` = '.(int) $this->ebay_profile->ebay_site_id);
        $ebay_alert = new EbayAlert($this->ebay);
        $root_category = Category::getRootCategory();
        $categories_ps = Category::getCategories($this->ebay_profile->id_lang);
        $category_list = $this->ebay->getChildCategories($categories_ps, $root_category->id_parent, array(), '');

        $smarty_vars = array(
            'category_alerts'         => $this->_getAlertCategories(),
            'path'                    => $this->path,
            'nb_products'             => $nb_products ? $nb_products : 0,
            'nb_products_mode_a'      => $nb_products_mode_a ? $nb_products_mode_a : 0,
            'nb_products_mode_b'      => $nb_products_mode_b ? $nb_products_mode_b : 0,
            'nb_products_sync_url'    => $nb_products_sync_url,
            'sync_products_url'       => $sync_products_url,
            'sync_message_exit'       => $this->ebay->l('A synchronization is currently underway. If you leave this page, it will be abandoned.', 'ebayformebaysynctab'),
            'action_url'              => $action_url,
            'ebay_sync_option_resync' => $this->ebay_profile->getConfiguration('EBAY_SYNC_OPTION_RESYNC'),
            'categories'              => $categories,
            'sync_1'                  => (Tools::getValue('section') == 'sync' && Tools::getValue('ebay_sync_mode') == "1" && Tools::getValue('btnSubmitSyncAndPublish')),
            'sync_2'                  => (Tools::getValue('section') == 'sync' && Tools::getValue('ebay_sync_mode') == "2" && Tools::getValue('btnSubmitSyncAndPublish')),
            'is_sync_mode_b'          => ($this->ebay_profile->getConfiguration('EBAY_SYNC_PRODUCTS_MODE') == 'B'),
            'ebay_sync_mode'          => (int)($this->ebay_profile->getConfiguration('EBAY_SYNC_MODE') ? $this->ebay_profile->getConfiguration('EBAY_SYNC_MODE') : 2),
            'prod_str'                => $nb_products >= 2 ? $this->ebay->l('products', 'ebayformebaysynctab') : $this->ebay->l('product', 'ebayformebaysynctab'),
            'admin_path'              => basename(_PS_ADMIN_DIR_),
            'load_kb_path'            => _MODULE_DIR_ . 'ebay/ajax/loadKB.php',
            'img_alert'               => $ebay_alert->checkNumberPhoto(),
            'PAYEMENTS' =>EbayBussinesPolicies::getPoliciesbyType('PAYMENT', $this->ebay_profile->id),
            'RETURN_POLICY' => EbayBussinesPolicies::getPoliciesbyType('RETURN_POLICY', $this->ebay_profile->id),
            'storeCategories' =>  EbayStoreCategory::getCategoriesWithConfiguration($this->ebay_profile->id),
            'ebayCategories' => $ebay_category_list,
            'ps_categories' => $category_list,
            'conditions' => $this->_translatePSConditions(EbayCategoryConditionConfiguration::getPSConditions()),
            'possible_attributes' => AttributeGroup::getAttributesGroups($this->context->cookie->id_lang),
            'possible_features' => Feature::getFeatures($this->context->cookie->id_lang, true),
            'id_lang' => $this->ebay_profile->id_lang,

        );

        return $this->display('formEbaySync.tpl', $smarty_vars);
    }

    public function postProcess()
    {
        var_dump($_POST);die;
        // Update Sync Option
        if ($this->ebay_profile == null) {
            return;
        }

        $this->ebay_profile->setConfiguration('EBAY_SYNC_OPTION_RESYNC', (Tools::getValue('ebay_sync_option_resync') == 1 ? 1 : 0));

        // Empty error result
        $this->ebay_profile->setConfiguration('EBAY_SYNC_LAST_PRODUCT', 0);

        if (file_exists(dirname(__FILE__).'/../../log/syncError.php')) {
            @unlink(dirname(__FILE__).'/../../log/syncError.php');
        }

        $this->ebay_profile->setConfiguration('EBAY_SYNC_MODE', Tools::getValue('ebay_sync_mode'));

        if (Tools::getValue('ebay_sync_products_mode') == 'A') {
            $this->ebay_profile->setConfiguration('EBAY_SYNC_PRODUCTS_MODE', 'A');
        } else {
            $this->ebay_profile->setConfiguration('EBAY_SYNC_PRODUCTS_MODE', 'B');

            // Select the sync Categories and Retrieve product list for eBay (which have matched and sync categories)
            if (Tools::getValue('category')) {
                EbayCategoryConfiguration::updateByIdProfile($this->ebay_profile->id, array('sync' => 0));
                foreach (Tools::getValue('category') as $id_category) {
                    EbayCategoryConfiguration::updateByIdProfileAndIdCategory((int) $this->ebay_profile->id, $id_category, array('id_ebay_profile' => $this->ebay_profile->id, 'sync' => 1));
                }
            }
        }
    }

    /*
     *
     * Get alert to see if some multi variation product on PrestaShop were added to a non multi sku categorie on ebay
     *
     */
    private function _getAlertCategories()
    {
        $alert = '';

        $cat_with_problem = EbayCategoryConfiguration::getMultiVarToNonMultiSku($this->ebay_profile, $this->context);

        $var = implode(', ', $cat_with_problem);

        if (count($cat_with_problem) > 0) {
            if (count($cat_with_problem) == 1) {
                $alert = $this->ebay->l('You have chosen eBay category : ', 'ebayformebaysynctab').' "'.$var.'" '.$this->ebay->l(' which does not support multivariation products. Each variation of a product will generate a new product in eBay', 'ebayformebaysynctab');
            } else {
                $alert = $this->ebay->l('You have chosen eBay categories : ', 'ebayformebaysynctab').' "'.$var.'" '.$this->ebay->l(' which do not support multivariation products. Each variation of a product will generate a new product in eBay', 'ebayformebaysynctab');
            }

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
}
