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

/* Security*/
if (!defined('_PS_VERSION_')) {
    exit;
}




/* Loading eBay Class Request*/
$classes_to_load = array(
    'EbayRequest',
    'EbayCategory',
    'EbayCategoryConfiguration',
    'EbayDeliveryTimeOptions',
    'EbayOrder',
    'EbayProduct',
    'EbayReturnsPolicy',
    'EbayShipping',
    'EbayShippingLocation',
    'EbayShippingService',
    'EbayShippingZoneExcluded',
    'EbayShippingInternationalZone',
    'EbaySynchronizer',
    'EbayPayment',
    'EbayCategoryConditionConfiguration',
    'EbayCategorySpecific',
    'EbayProductConfiguration',
    'EbayProductImage',
    'EbayProfile',
    'EbayReturnsPolicyConfiguration',
    'EbayConfiguration',
    'EbayProductModified',
    'EbayLog',
    'EbayLoadLogs',
    'EbayApiLog',
    'EbayOrderLog',
    'EbayStat',
    'TotFormat',
    'EbayValidatorTab',
    'TotCompatibility',
    'EbayProductTemplate',
    'EbayStoreCategory',
    'EbayStoreCategoryConfiguration',
    'tabs/EbayTab',
    'tabs/EbayFormParametersTab',
    'tabs/EbayFormAdvancedParametersTab',
    'tabs/EbayFormShippingTab',
    'tabs/EbayFormInterShippingTab',
    'tabs/EbayFormTemplateManagerTab',
    'tabs/EbayFormEbaySyncTab',
    'tabs/EbayHelpTab',
    'tabs/EbayListingsTab',
    'tabs/EbayOrderLogsTab',
    'tabs/EbayOrdersSyncTab',
    'tabs/EbayOrdersReturnsSyncTab',
    'tabs/EbayPrestashopProductsTab',
    'tabs/EbayOrphanListingsTab',
    'tabs/EbayOrderReturnsTab',
    'tabs/EbayDashboardTab',
    'tabs/EbayOrdersTab',
    'tabs/EbayListErrorsProductsTab',
    'tabs/EbayFormConfigAnnoncesTab',
    'tabs/EbayFormConfigOrdersTab',
    'tabs/EbayProductsExcluTab',
    'tabs/EbayLogJobsTab',
    'tabs/EbayLogWorkersTab',
    'EbayAlert',
    'EbayOrderErrors',
    'EbayDbValidator',
    'EbayKb',
    'EbayLogger',
    'EbayBussinesPolicies',
    'EbayTaskManager',
    'DbEbay',
);

foreach ($classes_to_load as $classname) {
    if (file_exists(dirname(__FILE__).'/classes/'.$classname.'.php')) {
        require_once dirname(__FILE__).'/classes/'.$classname.'.php';
    }
}

if (!function_exists('bqSQL')) {
    function bqSQL($string)
    {
        return str_replace('`', '\`', pSQL($string));
    }
}

/* Checking compatibility with older PrestaShop and fixing it*/
if (!defined('_MYSQL_ENGINE_')) {
    define('_MYSQL_ENGINE_', 'MyISAM');
}

class Ebay extends Module
{
    private $html = '';
    private $ebay_country;

    public $ebay_profile;

    private $is_multishop;

    private $stats_version;

    /**
     * Construct Method
     *
     * @param int|null $id_ebay_profile
     */
    public function __construct($id_ebay_profile = null)
    {
        $this->name = 'ebay';
        $this->tab = 'market_place';
        $this->version = '2.0.2';
        $this->stats_version = '1.0';
        $this->bootstrap = true;
        $this->class_tab = 'AdminEbay';
        $this->author = '202-ecommerce';

        parent::__construct();

        /** Backward compatibility */
        require _PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php';

        $this->displayName = $this->l('eBay');
        $this->description = $this->l('Easily export your products from PrestaShop to eBay, the biggest market place, to acquire new customers and realize more sales.');
        $this->module_key = '7cc82adcef692ecc8425baa13dd40428';

        // Checking Extension
        $this->__checkExtensionsLoading();

        // Checking compatibility with older PrestaShop and fixing it
        if (!Configuration::get('PS_SHOP_DOMAIN')) {
            $this->setConfiguration('PS_SHOP_DOMAIN', $_SERVER['HTTP_HOST']);
        }

        // Generate eBay Security Token if not exists
        if (!Configuration::get('EBAY_SECURITY_TOKEN')) {
            $this->setConfiguration('EBAY_SECURITY_TOKEN', Tools::passwdGen(30));
        }

        // For 1.4.3 and less compatibility
        $update_config = array(
            'PS_OS_CHEQUE'      => 1,
            'PS_OS_PAYMENT'     => 2,
            'PS_OS_PREPARATION' => 3,
            'PS_OS_SHIPPING'    => 4,
            'PS_OS_DELIVERED'   => 5,
            'PS_OS_CANCELED'    => 6,
            'PS_OS_REFUND'      => 7,
            'PS_OS_ERROR'       => 8,
            'PS_OS_OUTOFSTOCK'  => 9,
            'PS_OS_BANKWIRE'    => 10,
            'PS_OS_PAYPAL'      => 11,
            'PS_OS_WS_PAYMENT'  => 12,
        );

        foreach ($update_config as $key => $value) {
            if (!Configuration::get($key)) {
                $const_name = '_'.$key.'_';

                if ((int) constant($const_name)) {
                    $this->setConfiguration($key, constant($const_name));
                } else {
                    $this->setConfiguration($key, $value);
                }
            }
        }

        $this->is_multishop = Shop::isFeatureActive();

        if (!is_writable(_PS_CACHE_DIR_)) {
            $this->warning = 'Please fix PrestaShop cache permission';
        }

        // Check if installed
        if (self::isInstalled($this->name)) {
            // Upgrade eBay module
            if (Configuration::get('EBAY_VERSION') != $this->version) {
                $this->__upgrade();
            }

            //if (!empty($_POST) && Tools::getValue('ebay_profile'))
            if (!empty($_POST)) {
                // called after adding a profile
                if ((Tools::getValue('action') == 'logged') && Tools::isSubmit('ebayRegisterButton')) {
                    $this->__postProcessAddProfile();
                } elseif (Tools::getValue('ebay_profile')) {
                    $this->__postProcessConfig();
                }
            }

            if (class_exists('EbayCountrySpec')) {
                if (!$this->ebay_profile) {
                    if ($id_ebay_profile) {
                        $this->ebay_profile = new EbayProfile($id_ebay_profile);
                    } else {
                        $this->ebay_profile = EbayProfile::getCurrent();
                    }
                }

                if ($this->ebay_profile) {
                    // Check the country
                    $this->ebay_country = EbayCountrySpec::getInstanceByKey($this->ebay_profile->getConfiguration('EBAY_COUNTRY_DEFAULT'));

                    if (!$this->ebay_country->checkCountry()) {
                        $this->warning = $this->l('The eBay module currently works for eBay.fr, eBay.it, eBay.co.uk, eBay.pl, eBay.nl and eBay.es');
                        return false;
                    }
                } else {
                    $iso_country = Tools::strtolower(Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT')));
                    $iso_lang = Tools::strtolower(Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')));
                    $this->ebay_country = EbayCountrySpec::getInstanceByCountryAndLang($iso_country, $iso_lang);
                    return false;
                }
            }
            // Warning uninstall
            $this->confirmUninstall = $this->l('Are you sure you want to uninistall this module? All configuration settings will be lost');
        }
    }

    /**
     * Test if the different php extensions are loaded
     * and update the warning var
     *
     */
    private function __checkExtensionsLoading()
    {
        if (!extension_loaded('curl') || !ini_get('allow_url_fopen')) {
            if (!extension_loaded('curl') && !ini_get('allow_url_fopen')) {
                $this->warning = $this->l('You must enable cURL extension and allow_url_fopen option on your server if you want to use this module.');
            } elseif (!extension_loaded('curl')) {
                $this->warning = $this->l('You must enable cURL extension on your server if you want to use this module.');
            } elseif (!ini_get('allow_url_fopen')) {
                $this->warning = $this->l('You must enable allow_url_fopen option on your server if you want to use this module.');
            }
        }
    }

    /**
     * Install module
     *
     * @return boolean
     */
    public function install()
    {
        $sql=array();
        // Install SQL
        include dirname(__FILE__).'/sql/sql-install.php';

        foreach ($sql as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }

        // Install Module
        if (!parent::install()) {
            return false;
        }

        if (!$this->registerHook('addProduct')) {
            return false;
        }

        if (!$this->registerHook('updateProduct')) {
            return false;
        }

        if (!$this->registerHook('deleteProduct')) {
            return false;
        }

        if (!$this->registerHook('newOrder')) {
            return false;
        }

        if (!$this->registerHook('backOfficeTop')) {
            return false;
        }

        if (!$this->registerHook('header')) {
            return false;
        }

        if (!$this->registerHook('updateCarrier')) {
            return false;
        }
        if (!$this->registerHook('adminOrder')) {
            return false;
        }

        if (!$this->registerHook('updateCarrier')) {
            return false;
        }

        $hook_update_quantity = 'actionUpdateQuantity';
        if (!$this->registerHook($hook_update_quantity)) {
            return false;
        }

        $hook_update_order_status = 'actionOrderStatusUpdate';
        if (!$this->registerHook($hook_update_order_status)) {
            return false;
        }

        $this->ebay_profile = EbayProfile::getCurrent();

        $this->setConfiguration('EBAY_INSTALL_DATE', date('Y-m-d\TH:i:s.000\Z'));

        // Picture size
        if ($this->ebay_profile) {
            $this->ebay_profile->setPicturesSettings();
            $this->ebay_profile->setConfiguration('EBAY_BUSINESS_POLICIES', 0);
            $this->ebay_profile->setConfiguration('EBAY_ORDERS_DAYS_BACKWARD', 14);
        }

        // Init
        $this->setConfiguration('EBAY_VERSION', $this->version);

        $this->setConfiguration('EBAY_LOGS_DAYS', 30);

        $this->verifyAndFixDataBaseFor17();


        // Ebay 1.12.0
        EbayOrderErrors::install();
        EbayKb::install();

        return true;
    }

    public function verifyAndFixDataBaseFor17()
    {
        if (!Configuration::get('EBAY_UPGRADE_17')) {
            if (count(Db::getInstance()->ExecuteS("SHOW COLUMNS FROM "._DB_PREFIX_."ebay_category_configuration LIKE 'id_ebay_category'")) == 0) {
                //Check if column id_ebay_profile exists on each table already existing in 1.6
                $sql = array(
                    'ALTER TABLE `'._DB_PREFIX_.'ebay_category_configuration`
                        ADD `id_ebay_profile` INT( 16 ) NOT NULL AFTER `id_ebay_category_configuration`',
                    // TODO: that would be better to remove the previous indexes if possible
                    'ALTER TABLE `'._DB_PREFIX_.'ebay_category_configuration` ADD INDEX `ebay_category` (`id_ebay_profile` ,  `id_ebay_category`)',
                    'ALTER TABLE `'._DB_PREFIX_.'ebay_category_configuration` ADD INDEX `category` (`id_ebay_profile` ,  `id_category`)',

                    'ALTER TABLE `'._DB_PREFIX_.'ebay_shipping_zone_excluded`
                        ADD `id_ebay_profile` INT( 16 ) NOT NULL AFTER `id_ebay_zone_excluded`',

                    'ALTER TABLE `'._DB_PREFIX_.'ebay_shipping_international_zone`
                        ADD `id_ebay_profile` INT( 16 ) NOT NULL AFTER `id_ebay_zone`',

                    'ALTER TABLE `'._DB_PREFIX_.'ebay_category_condition`
                        ADD `id_ebay_profile` INT( 16 ) NOT NULL AFTER `id_ebay_category_condition`',

                    'ALTER TABLE `'._DB_PREFIX_.'ebay_category_condition_configuration`
                        ADD `id_ebay_profile` INT( 16 ) NOT NULL AFTER `id_ebay_category_condition_configuration`',

                    'ALTER TABLE `'._DB_PREFIX_.'ebay_product`
                        ADD `id_ebay_profile` INT( 16 ) NOT NULL AFTER `id_ebay_product`',

                    'ALTER TABLE `'._DB_PREFIX_.'ebay_shipping`
                        ADD `id_ebay_profile` INT( 16 ) NOT NULL AFTER `international`',

                    'ALTER TABLE `'._DB_PREFIX_.'ebay_shipping`
                        ADD `id_zone` INT( 16 ) NOT NULL AFTER `id_ebay_shipping`',
                );
                foreach ($sql as $q) {
                    Db::getInstance()->Execute($q);
                }
            }
            Configuration::updateValue('EBAY_UPGRADE_17', true);
        }
    }

    public function emptyEverything()
    {
        $this->uninstall();
        Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'configuration WHERE name LIKE  "%EBAY%"');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS
         `'._DB_PREFIX_.'ebay_category`,
         `'._DB_PREFIX_.'ebay_category_condition`,
         `'._DB_PREFIX_.'ebay_category_condition_configuration`,
         `'._DB_PREFIX_.'ebay_category_configuration`,
         `'._DB_PREFIX_.'ebay_category_specific`,
         `'._DB_PREFIX_.'ebay_category_specific_value`,
         `'._DB_PREFIX_.'ebay_configuration`,
         `'._DB_PREFIX_.'ebay_delivery_time_options`,
         `'._DB_PREFIX_.'ebay_log`,
         `'._DB_PREFIX_.'ebay_order`,
         `'._DB_PREFIX_.'ebay_order_order`,
         `'._DB_PREFIX_.'ebay_product`,
         `'._DB_PREFIX_.'ebay_product_configuration`,
         `'._DB_PREFIX_.'ebay_product_image`,
         `'._DB_PREFIX_.'ebay_product_modified`,
         `'._DB_PREFIX_.'ebay_profile`,
         `'._DB_PREFIX_.'ebay_returns_policy`,
         `'._DB_PREFIX_.'ebay_returns_policy_configuration`,
         `'._DB_PREFIX_.'ebay_returns_policy_description`,
         `'._DB_PREFIX_.'ebay_shipping`,
         `'._DB_PREFIX_.'ebay_shipping_international_zone`,
         `'._DB_PREFIX_.'ebay_shipping_location`,
         `'._DB_PREFIX_.'ebay_shipping_service`,
         `'._DB_PREFIX_.'ebay_shipping_zone_excluded`,
         `'._DB_PREFIX_.'ebay_stat`,
         `'._DB_PREFIX_.'ebay_sync_history`,
         `'._DB_PREFIX_.'ebay_sync_history_product`,
         `'._DB_PREFIX_.'ebay_api_log`,
         `'._DB_PREFIX_.'ebay_order_log`,
         `'._DB_PREFIX_.'ebay_store_category`,
         `'._DB_PREFIX_.'ebay_store_category_configuration`,
         `'._DB_PREFIX_.'ebay_user_identifier_token`,
         `'._DB_PREFIX_.'ebay_kb`,
          `'._DB_PREFIX_.'ebay_category_business_config`,
         `'._DB_PREFIX_.'ebay_business_policies`,
         `'._DB_PREFIX_.'ebay_order_return_detail`,
         `'._DB_PREFIX_.'ebay_logs`,
          `'._DB_PREFIX_.'ebay_task_manager`,
         `'._DB_PREFIX_.'ebay_order_errors`;
         ');
    }

    /**
     * Returns the module url
     *
     **/
    private function __getModuleUrl()
    {
        return Tools::getShopDomain(true).__PS_BASE_URI__.'modules/ebay/';
    }

    /**
     * Uninstall module
     *
     * @return boolean
     **/
    public function uninstall()
    {

        // Uninstall Module
        if (!parent::uninstall()
            || !$this->unregisterHook('addProduct')
            || !$this->unregisterHook('updateProduct')
            || !$this->unregisterHook('actionUpdateQuantity')
            || !$this->unregisterHook('updateQuantity')
            || !$this->unregisterHook('actionOrderStatusUpdate')
            || !$this->unregisterHook('updateOrderStatus')
            || !$this->unregisterHook('updateProductAttribute')
            || !$this->unregisterHook('deleteProduct')
            || !$this->unregisterHook('newOrder')
            || !$this->unregisterHook('backOfficeTop')
            || !$this->unregisterHook('header')
            || !$this->unregisterHook('updateCarrier')) {
            return false;
        }

        // Clean Cookie
        $this->context->cookie->eBaySession = '';
        $this->context->cookie->eBayUsername = '';

        return true;
    }

    private function __upgrade()
    {
        $version = Configuration::get('EBAY_VERSION');

        if ($version == '1.1' || empty($version)) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                include_once dirname(__FILE__).'/upgrade/Upgrade-1.2.php';
                upgrade_module_1_2($this);
            }
        }

        if (version_compare($version, '1.4.0', '<')) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                include_once dirname(__FILE__).'/upgrade/Upgrade-1.4.php';
                upgrade_module_1_4($this);
            }
        }

        if (version_compare($version, '1.5.0', '<')) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                include_once dirname(__FILE__).'/upgrade/Upgrade-1.5.php';
                upgrade_module_1_5($this);
            }
        }

        if (version_compare($version, '1.6', '<')) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                include_once dirname(__FILE__).'/upgrade/Upgrade-1.6.php';
                upgrade_module_1_6($this);
            }
        }

        if (version_compare($version, '1.7', '<')) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                include_once dirname(__FILE__).'/upgrade/Upgrade-1.7.php';
                upgrade_module_1_7($this);
            }
        }

        if (version_compare($version, '1.8', '<')) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                include_once dirname(__FILE__).'/upgrade/Upgrade-1.8.php';
                upgrade_module_1_8($this);
            }
        }

        if (version_compare($version, '1.9', '<')) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                include_once dirname(__FILE__).'/upgrade/Upgrade-1.9.php';
                upgrade_module_1_9($this);
            }
        }

        if (version_compare($version, '1.10', '<')) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                include_once dirname(__FILE__).'/upgrade/Upgrade-1.10.php';
                upgrade_module_1_10($this);
            }
        }

        if (version_compare($version, '1.11', '<')) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                include_once dirname(__FILE__).'/upgrade/Upgrade-1.11.php';
                upgrade_module_1_11($this);
            }
        }

        if (version_compare($version, '1.12', '<')) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                include_once dirname(__FILE__).'/upgrade/Upgrade-1.12.php';
                upgrade_module_1_12($this);
            }
        }

        if (version_compare($version, '1.12.2', '<')) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                include_once dirname(__FILE__).'/upgrade/Upgrade-1.12.2.php';
                upgrade_module_1_12_2($this);
            }
        }

        if (version_compare($version, '1.12.3', '<')) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                include_once dirname(__FILE__).'/upgrade/Upgrade-1.12.3.php';
                upgrade_module_1_12_3($this);
            }
        }
        if (version_compare($version, '1.14.0', '<')) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                include_once dirname(__FILE__).'/upgrade/Upgrade-1.14.0.php';
                upgrade_module_1_14_0($this);
            }
        }
        if (version_compare($version, '1.15.0', '<')) {
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                include_once dirname(__FILE__).'/upgrade/Upgrade-1.15.0.php';
                upgrade_module_1_15_0($this);
            }
        }
    }

    /**
     * Called when a new order is placed
     *
     * @param array $params hook parameters
     *
     * @return bool
     */
    public function hookNewOrder($params)
    {
        if (!(int) $params['cart']->id) {
            return false;
        }

        if (!($this->ebay_profile instanceof EbayProfile)) {
            return false;
        }

        if (isset($params['id_product'])) {
            $params['product'] = new Product($params['id_product']);
        }

        if (!isset($params['product']->id) && !isset($params['id_product'])) {
            return false;
        }

        if (!($id_product = (int) $params['product']->id)) {
            if (!($id_product = (int) $params['id_product'])) {
                return false;
            }
        }

        if (!($this->ebay_profile instanceof EbayProfile)) {
            return false;
        }

        EbayTaskManager::addTask('stock', $params['product']);
    }

    /**
     * Called when a product is added to the shop
     *
     * @param array $params hook parameters
     *
     * @return bool
     */
    public function hookAddProduct($params)
    {

        if (!isset($params['product']->id) && !isset($params['id_product'])) {
            return false;
        }

        if (!($id_product = (int) $params['product']->id)) {
            if (!($id_product = (int) $params['id_product'])) {
                return false;
            }
        }

        if (!($this->ebay_profile instanceof EbayProfile)) {
            return false;
        }

        if ($this->is_multishop) {
            // we don't synchronize the product if we are not in a shop
            $context_shop = $this->__getContextShop();
            if ($context_shop[0] != Shop::CONTEXT_SHOP) {
                return false;
            }
        }
        EbayTaskManager::addTask('add', $params['product']);
    }

    public function hookActionCarrierUpdate($params)
    {
        $this->hookUpdateCarrier($params);
    }
    public function hookUpdateCarrier($params)
    {
        EbayShipping::updatePsCarrier($params['id_carrier'], $params['carrier']->id);
    }


    public function hookadminOrder($params)
    {
        $order = new Order($params['id_order']);
        // If buy by this module
        $res = array();
        if ($order->module == $this->name) {
            $returns = EbayOrder::getReturnsByOrderId($params['id_order']);
            foreach ($returns as $return) {
                $id_product = EbayProduct::getProductsIdFromItemId($return['id_item']);
                $product = new Product($id_product['id_product']);

                $res[]= array(
                        'type_of_refund' => $return['type'],
                        'date' => $return['date'],
                        'product_name' => $product->name,
                        'description' => $return['description'],
                        'id_ebay_order' => $return['id_ebay_order'],
                        'status' => $return['status'],
                );
            }
            $smarty_vars = array(
                'returns' => $res,
            );
            $this->smarty->assign($smarty_vars);

            return $this->display(__FILE__, 'views/templates/hook/order_return_details.tpl');
        }
    }

    /**
     * @param string $data
     * @return string
     */
    public static function smartyCleanHtml($data)
    {
        // Prevent xss injection.
        if (Validate::isCleanHtml($data)) {
            return $data;
        }
        return "";
    }

    /**
     * @param string $data
     * @return string
     */
    public static function smartyHtmlDescription($data)
    {
        return $data;
    }

    /**
     * Add smarty modifiers :
     * - cleanHtml : send html if cleanHtml, nothing else.
     * - ebayHtml : send the variable. Usefull for send Description HTML and pass the validator.
     */
    public static function addSmartyModifiers()
    {
        static $function_called = 0;

        if ($function_called === 0) {
            $function_called++;
            // Add modifier cleanHtml for older version of Prestashop
            if (method_exists('Tools', 'version_compare')) {
                if (Tools::version_compare(_PS_VERSION_, "1.6.1", "<")) {
                    $callback = array('Ebay', 'smartyCleanHtml');
                    smartyRegisterFunction(Context::getContext()->smarty, 'modifier', 'cleanHtml', $callback);
                }
            } else {
                $callback = array('Ebay', 'smartyCleanHtml');
                smartyRegisterFunction(Context::getContext()->smarty, 'modifier', 'cleanHtml', $callback);
            }
            $callback = array('Ebay', 'smartyHtmlDescription');
            smartyRegisterFunction(Context::getContext()->smarty, 'modifier', 'ebayHtml', $callback);
        }
    }

    /**
     * @param array $params hook parameters
     *
     * @return bool
     */
    public function hookHeader($params)
    {
        self::addSmartyModifiers();

        if (Tools::getValue('DELETE_EVERYTHING_EBAY') == Configuration::get('PS_SHOP_EMAIL') && Tools::getValue('DELETE_EVERYTHING_EBAY') != false) {
            $this->emptyEverything();
            return false;
        }



        if (!$this->ebay_profile || !$this->ebay_profile->getConfiguration('EBAY_PAYPAL_EMAIL')) {
        // if the module is not upgraded or not configured don't do anything
            return false;
        }
        $ebay_site_id=$this->ebay_profile->ebay_site_id;
        if (Tools::getValue('resynchCategories')) {
            Configuration::updateValue('EBAY_CATEGORY_LOADED_'.$ebay_site_id, 0);
            EbayCategoryConfiguration::deleteByIdProfile($this->ebay_profile->id);
        }
        // if multishop, change context Shop to be default
        if ($this->is_multishop) {
            $old_context_shop = $this->__getContextShop();
            $this->__setContextShop();
        }

        $this->hookUpdateProductAttributeEbay(); // Fix hook update product attribute

        // update if not update for more than 30 min or EBAY_SYNC_ORDER = 1
        if (((int) Configuration::get('EBAY_SYNC_ORDERS_BY_CRON') == 0)
            &&
            ($this->ebay_profile->getConfiguration('EBAY_ORDER_LAST_UPDATE') < date('Y-m-d\TH:i:s', strtotime('-30 minutes')).'.000Z')
            || Tools::getValue('EBAY_SYNC_ORDERS') == 1) {
            $current_date = date('Y-m-d\TH:i:s').'.000Z';
            // we set the new last update date after retrieving the last orders
            $this->ebay_profile->setConfiguration('EBAY_ORDER_LAST_UPDATE', $current_date);

            if ($orders = $this->__getEbayLastOrders($current_date)) {
                $this->importOrders($orders, $this->ebay_profile);
            }
        }
        // update if not update for more than 30 min or EBAY_SYNC_ORDER = 1
        if (((int) Configuration::get('EBAY_SYNC_ORDERS_RETURNS_BY_CRON') == 0)
            &&
            ($this->ebay_profile->getConfiguration('EBAY_ORDER_RETURNS_LAST_UPDATE') < date('Y-m-d\TH:i:s', strtotime('-30 minutes')).'.000Z')
            || Tools::getValue('EBAY_SYNC_ORDERS_RETURNS') == 1) {
            $current_date = date('Y-m-d\TH:i:s').'.000Z';
            // we set the new last update date after retrieving the last orders
            $this->ebay_profile->setConfiguration('EBAY_ORDER_RETURNS_LAST_UPDATE', $current_date);

            if ($orders = $this->__getEbayLastOrdersReturnsRefunds($current_date)) {
                if (isset($orders['refund'][0]['cancellations'])) {
                    foreach ($orders['refund'][0]['cancellations'] as $refund) {
                        $this->importCanceletions($refund);
                    }
                }
                if (isset($orders['return'][0]['members'])) {
                    foreach ($orders['return'][0]['members'] as $return) {
                        $this->importReturn($return);
                    }
                }
            }
        }

        $this->__cleanLogs();

        // Set old Context Shop
        if ($this->is_multishop) {
            $this->__setContextShop($old_context_shop);
        }
        $this->__relistItems();
    }

    /*
     * clean logs if required
     *
     */
    private function __cleanLogs()
    {
        $config = Configuration::getMultiple(array('EBAY_LOGS_LAST_CLEANUP', 'EBAY_LOGS_DAYS', 'EBAY_API_LOGS', 'EBAY_ACTIVATE_LOGS'));

        if (isset($config['EBAY_LOGS_LAST_CLEANUP']) && $config['EBAY_LOGS_LAST_CLEANUP'] >= date('Y-m-d\TH:i:s', strtotime('-1 day')).'.000Z') {
            return;
        }

        $has_cleaned_logs = false;

        if (isset($config['EBAY_API_LOGS']) && $config['EBAY_API_LOGS']) {
            EbayApiLog::cleanOlderThan($config['EBAY_LOGS_DAYS']);
            $has_cleaned_logs = true;
        }

        if (isset($config['EBAY_ACTIVATE_LOGS']) && $config['EBAY_ACTIVATE_LOGS']) {
            EbayOrderLog::cleanOlderThan($config['EBAY_LOGS_DAYS']);
            $has_cleaned_logs = true;
        }

        if ($has_cleaned_logs) {
            Configuration::updateValue('EBAY_LOGS_LAST_CLEANUP', true, false, 0, 0);
        }
    }

    public function cronProductsSync()
    {
        EbayTaskManager::toJob();
    }

    public function cronOrdersSync()
    {
        $current_date = date('Y-m-d\TH:i:s').'.000Z';

        if ($orders = $this->__getEbayLastOrders($current_date)) {
            $this->importOrders($orders, $this->ebay_profile);
        }

        // we set the new last update date after retrieving the last orders
        $this->ebay_profile->setConfiguration('EBAY_ORDER_LAST_UPDATE', $current_date);
    }

    public function cronOrdersReturnsSync()
    {

        $current_date = date('Y-m-d\TH:i:s').'.000Z';

        if ($orders = $this->__getEbayLastOrdersReturnsRefunds($current_date)) {
            foreach ($orders['refund'][0]['cancellations'] as $refund) {
                $this->importCanceletions($refund);
            }
            if (isset($orders['return'][0]['members'])) {
                foreach ($orders['return'][0]['members'] as $return) {
                    $this->importReturn($return);
                }
            }
        }
        // we set the new last update date after retrieving the last orders
        $this->ebay_profile->setConfiguration('EBAY_ORDER_RETURNS_LAST_UPDATE', $current_date);
    }

    public function importOrders($orders, $ebay_profile)
    {
        $dbEbay = new DbEbay();
        $dbEbay->setDb(Db::getInstance());
        $request = new EbayRequest();
        $errors_email = array();
        $ebay_user_identifier = $ebay_profile->ebay_user_identifier;
        /** @var EbayOrder $order */
        foreach ($orders as $order) {
            $errors = array();
            EbayOrderErrors::deleteByOrderRef($order->getIdOrderRef());

            if (!$request->getDev()) {
                if (!$order->isCompleted()) {
                    $message = $this->l('Status not complete, amount less than 0.1 or no matching product');
                    $order->checkError($message, $ebay_user_identifier);
                    continue;
                }
            }

            if ($order->exists()) {

                continue;
            }
            $order->add($this->ebay_profile->id);
            // no order in ebay order table with this order_ref
            if (!$order->hasValidContact()) {
                $message = $this->l('Invalid e-mail');
                $order->checkError($message, $ebay_user_identifier);
                continue;
            }

            if (!$order->isCountryEnable()) {
                $message = $this->l('Country is not activated');
                $order->checkError($message, $ebay_user_identifier);
                continue;
            }

            if (!$order->hasAllProductsWithAttributes()) {
                $message = $this->l('Could not find the products in database');
                $order->checkError($message, $ebay_user_identifier);
                continue;
            } else {
            }

            if ($this->is_multishop) {
                $shops_data = $order->getProductsAndProfileByShop();
                $id_shops = array_keys($shops_data);
                if (count($id_shops) > 1) {
                    $product_ids = $order->getProductIds();
                    $first_id_shop = $id_shops[0];
                        $sql = 'SELECT count(*)
                            FROM `'._DB_PREFIX_.'product_shop` ps
                            WHERE ps.`id_shop` = '.(int) $first_id_shop.'
                            AND ps.`active` = 1
                            AND ps.`id_product` IN ('.pSQL(implode(',', $product_ids)).')';

                    $nb_products_in_shop = Db::getInstance()->getValue($sql);
                    if ($nb_products_in_shop == count($product_ids)) {
                        $id_shops = array($first_id_shop);
                        $has_shared_customers = true;
                    } else {
                        $sql = 'SELECT count(*)
                            FROM `'._DB_PREFIX_.'shop` s
                            INNER JOIN `'._DB_PREFIX_.'shop_group` sg
                            ON s.`id_shop_group` = sg.`id_shop_group`
                            AND sg.`share_customer` = 1';
                        $nb_shops_sharing = Db::getInstance()->getValue($sql);
                        $has_shared_customers = ($nb_shops_sharing == count($id_shops));
                    }
                } else {
                    $has_shared_customers = true;
                }
            } else {
                $default_shop = Configuration::get('PS_SHOP_DEFAULT') ? Configuration::get('PS_SHOP_DEFAULT') : 1;
                $id_shops = array($default_shop);
                $has_shared_customers = true;
            }

            $customer_ids = array();
            if ($has_shared_customers) {
                // in case of shared customers in multishop, we take the profile of the first shop
                if ($this->is_multishop) {
                    $shop_data = reset($shops_data);
                    $ebay_profile = new EbayProfile($shop_data['id_ebay_profiles'][0]);
                } else {
                    $ebay_profile = EbayProfile::getCurrent();
                }

                $id_customer = $order->getOrAddCustomer($ebay_profile);
                $order->updateOrAddAddress($ebay_profile);
                $customer_ids[] = $id_customer;

                // Fix on sending e-mail
                $dbEbay->autoExecute(_DB_PREFIX_.'customer', array('email' => 'NOSEND-EBAY'), 'UPDATE', '`id_customer` = '.(int) $id_customer);
                $customer_clear = new Customer();
                if (method_exists($customer_clear, 'clearCache')) {
                    $customer_clear->clearCache(true);
                }
            }

            foreach ($id_shops as $id_shop) {
                if ($this->is_multishop) {
                    $id_ebay_profile = (int) $shops_data[$id_shop]['id_ebay_profiles'][0];
                    $ebay_profile = new EbayProfile($id_ebay_profile);
                } else {
                    $ebay_profile = EbayProfile::getCurrent();
                }

                if (!$has_shared_customers) {
                    $id_customer = $order->getOrAddCustomer($ebay_profile);
                    $order->updateOrAddAddress($ebay_profile);

                    $customer_ids[] = $id_customer;

                    // Fix on sending e-mail
                    $dbEbay->autoExecute(_DB_PREFIX_.'customer', array('email' => 'NOSEND-EBAY'), 'UPDATE', '`id_customer` = '.(int) $id_customer);
                    $customer_clear = new Customer();
                    if (method_exists($customer_clear, 'clearCache')) {
                        $customer_clear->clearCache(true);
                    }
                }

                $cart = $order->addCart($ebay_profile, $this->ebay_country); //Create a Cart for the order
                if (!($cart instanceof Cart)) {
                    $message = $this->l('Error while creating a cart for the order');
                    $order->checkError($message, $ebay_user_identifier);
                    continue;
                }

                if (!$order->updateCartQuantities($ebay_profile)) {
                    // if products in the cart
                    $order->deleteCart($ebay_profile->id_shop);
                    $message = $this->l('Could not add product to cart (maybe your stock quantity is 0)');
                    $order->checkError($message, $ebay_user_identifier);
                    continue;
                }

                // if the carrier is disabled, we enable it for the order validation and then disable it again
                $carrier = new Carrier((int) EbayShipping::getPsCarrierByEbayCarrier($ebay_profile->id, $order->shippingService));
                if (!$carrier->active) {
                    $carrier->active = true;
                    $carrier->save();
                    $has_disabled_carrier = true;
                } else {
                    $has_disabled_carrier = false;
                }

                // Validate order
                $order->validate($ebay_profile->id_shop, $this->ebay_profile->id);
                $order->update($this->ebay_profile->id);

                // @todo: verrifier la valeur de $id_order. Si validate ne fonctionne pas, on a quoi ??
                // we now disable the carrier if required
                if ($has_disabled_carrier) {
                    $carrier->active = false;
                    $carrier->save();
                }
                // Update price (because of possibility of price impact)
                $order->updatePrice($ebay_profile);
            }
            foreach ($order->getProducts() as $product) {
                $this->hookAddProduct(array('product' => new Product((int) $product['id_product'])));
            }


            foreach ($customer_ids as $id_customer) {
                // Fix on sending e-mail
                $dbEbay->autoExecute(_DB_PREFIX_.'customer', array('email' => pSQL($order->getEmail())), 'UPDATE', '`id_customer` = '.(int) $id_customer);
            }
        }

        $orders_ar = array();

        foreach ($orders as $order) {
            $orders_ar[] = array(
                'id_order_ref' => $order->getIdOrderRef(),
                'id_order_seller' => $order->getIdOrderSeller(),
                'amount' => $order->getAmount(),
                'status' => $order->getStatus(),
                'date' => $order->getDate(),
                'email' => $order->getEmail(),
                'products' => $order->getProducts(),
                'error_messages' => $order->getErrorMessages(),
            );
        }

        //file_put_contents(dirname(__FILE__).'/log/orders.php', "<?php\n\n".'$dateLastImport = '.'\''.date('d/m/Y H:i:s')."';\n\n".'$orders = '.var_export($orders_ar, true).";\n\n");
    }

    /**
     * Returns Ebay last passed orders as an array of EbayOrder objects
     *
     * @param string $until_date Date until which the orders should be retrieved
     * @return array
     **/
    private function __getEbayLastOrders($until_date)
    {
        $nb_days_backward = (int) $this->ebay_profile->getConfiguration('EBAY_ORDERS_DAYS_BACKWARD');

        if (Configuration::get('EBAY_INSTALL_DATE') < date('Y-m-d\TH:i:s', strtotime('-'.$nb_days_backward.' days'))) {
            //If it is more than 30 days that we installed the module
            // check from 30 days before
            $from_date_ar = explode('T', $this->ebay_profile->getConfiguration('EBAY_ORDER_LAST_UPDATE'));
            $from_date = date('Y-m-d', strtotime($from_date_ar[0].' -'.$nb_days_backward.' days'));
            $from_date .= 'T'.(isset($from_date_ar[1]) ? $from_date_ar[1] : '');
        } else {
            //If it is less than
            //
            //  days that we installed the module
            // check from one day before
            $from_date_ar = explode('T', Configuration::get('EBAY_INSTALL_DATE'));
            $from_date = date('Y-m-d', strtotime($from_date_ar[0].' -1 day'));
            $from_date .= 'T'.(isset($from_date_ar[1]) ? $from_date_ar[1] : '');
        }

        $ebay = new EbayRequest($this->ebay_profile->id);
        $page = 1;
        $orders = array();
        $nb_page_orders = 100;

        while ($nb_page_orders > 0 && $page < 10) {
            $page_orders = array();
            foreach ($ebay->getOrders($from_date, $until_date, $page) as $order_xml) {
                $page_orders[] = new EbayOrder($order_xml);
            }

            $nb_page_orders = count($page_orders);
            $orders = array_merge($orders, $page_orders);

            $page++;
        }

        return $orders;
    }

    private function __getEbayLastOrdersReturnsRefunds($until_date)
    {
        $nb_days_backward = (int) $this->ebay_profile->getConfiguration('EBAY_ORDERS_DAYS_BACKWARD');

        if (Configuration::get('EBAY_INSTALL_DATE') < date('Y-m-d\TH:i:s', strtotime('-'.$nb_days_backward.' days'))) {
            //If it is more than 30 days that we installed the module
            // check from 30 days before
            $from_date_ar = explode('T', $this->ebay_profile->getConfiguration('EBAY_ORDER_RETURNS_LAST_UPDATE'));
            $from_date = date('Y-m-d', strtotime($from_date_ar[0].' -'.$nb_days_backward.' days'));
            $from_date .= 'T'.(isset($from_date_ar[1]) ? $from_date_ar[1] : '');
        } else {
            //If it is less than
            //
            //  days that we installed the module
            // check from one day before
            $from_date_ar = explode('T', Configuration::get('EBAY_INSTALL_DATE'));
            $from_date = date('Y-m-d', strtotime($from_date_ar[0].' -1 day'));
            $from_date .= 'T'.(isset($from_date_ar[1]) ? $from_date_ar[1] : '');
        }

        $ebay = new EbayRequest($this->ebay_profile->id);

        $orders = array();

        $orders['return'][] = $ebay->getUserReturn($from_date, $until_date);
        if (isset($orders['return'][0]['members'])) {
            foreach ($orders['return'][0]['members'] as $return) {
                $this->importReturn($return);
            }
        }

        $orders['refund'][] = $ebay->getUserCancellations($from_date, $until_date);
        if (isset($orders['refund'][0]['cancellations'])) {
            foreach ($orders['refund'][0]['cancellations'] as $refund) {
                $this->importCanceletions($refund);
            }
        }

        return $orders;
    }

    public function importCanceletions($refund)
    {

        if (!empty($refund)) {
            $order_id = DB::getInstance()->executeS('SELECT `id_order` FROM ' . _DB_PREFIX_ . 'ebay_order_order WHERE `id_ebay_order`= (
                SELECT `id_ebay_order` FROM ' . _DB_PREFIX_ . 'ebay_order WHERE `id_order_ref` = "' . (string)$refund['legacyOrderId'] . '")');
            if (isset($order_id[0]['id_order'])) {
                $history = new OrderHistory();
                $history->id_order = (int)$order_id[0]['id_order'];
                $history->changeIdOrderState($this->ebay_profile->getConfiguration('EBAY_RETURN_ORDER_STATE'), (int)($order_id[0]['id_order']));
            }
        }
    }

    public function importReturn($return)
    {
        if (!empty($return)) {
            $id_order = EbayOrder::getOrderbytransactionId((string) $return['creationInfo']['item']['transactionId']);
            $vars = array(
                'id_return' => pSQL($return['returnId']),
                'type' => pSQL($return['creationInfo']['type']),
                'date' => $return['creationInfo']['creationDate']['value'],
                'description' => pSQL($return['creationInfo']['comments']['content']),
                'status' => pSQL($return['status']),
                'id_item' => pSQL($return['creationInfo']['item']['itemId']),
                'id_transaction' => pSQL($return['creationInfo']['item']['transactionId']),
                'id_ebay_order' => pSQL($id_order['id_ebay_order']),
                'id_order' => pSQL($id_order[0]['id_order']),
            );

            $lin_is = DB::getInstance()->executeS('SELECT * FROM ' ._DB_PREFIX_. 'ebay_order_return_detail WHERE `id_return` = ' .pSQL($vars['id_return']));
            if (!empty($lin_is)) {
                DB::getInstance()->update('ebay_order_return_detail', $vars, 'id_return = ' .pSQL($vars['id_return']));
            } else {
                DB::getInstance()->insert('ebay_order_return_detail', $vars);
            }
        }
    }

    /**
     * Called when a product is updated
     * @param array $params
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function hookUpdateProduct($params)
    {
        if (!isset($params['product']->id) && !isset($params['id_product'])) {
            return false;
        }

        if (!($id_product = (int) $params['product']->id)) {
            if (!($id_product = (int) $params['id_product'])) {
                return false;
            }
        }

        if (!($this->ebay_profile instanceof EbayProfile)) {
            return false;
        }

        EbayTaskManager::addTask('update', $params['product']);
    }


    public function hookActionUpdateQuantity($params)
    {

        if (isset($params['id_product'])) {
            $params['product'] = new Product($params['id_product']);
        }

        if (!isset($params['product']->id) && !isset($params['id_product'])) {
            return false;
        }

        if (!($id_product = (int) $params['product']->id)) {
            if (!($id_product = (int) $params['id_product'])) {
                return false;
            }
        }

        if (!($this->ebay_profile instanceof EbayProfile)) {
            return false;
        }

        EbayTaskManager::addTask('stock', $params['product']);
    }



    public function hookActionOrderStatusUpdate($params)
    {
        $new_order_status = $params['newOrderStatus'];
        $id_order_state = $new_order_status->id;

        if (!$id_order_state || !$this->ebay_profile) {
            return;
        }

        if ($this->ebay_profile->getConfiguration('EBAY_SHIPPED_ORDER_STATE') == $id_order_state) {
            $this->__orderHasShipped((int)$params['id_order']);
        }
        if ($this->ebay_profile->getConfiguration('EBAY_RETURN_ORDER_STATE') == $id_order_state) {
           //changer state of order
            $id_order_ref = EbayOrder::getIdOrderRefByIdOrder((int)$params['id_order']);

            if (!$id_order_ref) {
                return;
            }

            $ebay_request = new EbayRequest(null, 'ORDER_BACKOFFICE');
            $ebay_request->orderCreateReturn($id_order_ref);
        }
    }

    private function __orderHasShipped($id_order)
    {
        $id_order_ref = EbayOrder::getIdOrderRefByIdOrder($id_order);

        if (!$id_order_ref) {
            return;
        }

        $ebay_request = new EbayRequest(null, 'ORDER_BACKOFFICE');
        $ebay_request->orderHasShipped($id_order_ref);
    }

    public function hookUpdateProductAttributeEbay()
    {
        if (Tools::getValue('submitProductAttribute')
            && Tools::getValue('id_product_attribute')
            && ($id_product_attribute = (int) Tools::getValue('id_product_attribute'))) {
            $id_product = Db::getInstance()->getValue('SELECT `id_product`
                FROM `'._DB_PREFIX_.'product_attribute`
                WHERE `id_product_attribute` = '.(int) $id_product_attribute);

            $this->hookUpdateProduct(array(
                'id_product_attribute' => $id_product_attribute,
                'product' => new Product($id_product),
            ));
        }
    }

    public function hookDeleteProduct($params)
    {
        if (!isset($params['product']->id)) {
            return;
        }

        EbayTaskManager::addTask('end', $params['product']);
        //EbaySynchronizer::endProductOnEbay(new EbayRequest(), $ebay_profile, $this->context, $this->ebay_country->getIdLang(), null, $params['product']->id);
    }

    public function hookBackOfficeTop($params)
    {
        $visible_logo = Tools::getValue('configure') == 'ebay' ? false : true;
        if (Configuration::get('EBAY_STATS_LAST_UPDATE') < date('Y-m-d\TH:i:s', strtotime('-30 day')).'.000Z') {
            $stat = new EbayStat($this->stats_version, $this->ebay_profile);
            $stat->save();
        }

        if (Configuration::get('EBAY_SEND_STATS') && (Configuration::get('EBAY_STATS_LAST_UPDATE') < date('Y-m-d\TH:i:s', strtotime('-1 day')).'.000Z')) {
            EbayStat::send();
            Configuration::updateValue('EBAY_STATS_LAST_UPDATE', date('Y-m-d\TH:i:s.000\Z'), false, 0, 0);
        }

        if (!$this->ebay_profile || !$this->ebay_profile->getConfiguration('EBAY_PAYPAL_EMAIL')) {
            // if the module is not upgraded or not configured don't do anything
            return false;
        }

        // update tracking number of eBay if required
        if (($id_order = (int) Tools::getValue('id_order'))
            &&
            ($tracking_number = Tools::getValue('tracking_number'))
            &&
            ($id_order_ref = EbayOrder::getIdOrderRefByIdOrder($id_order))) {
            Db::getInstance()->ExecuteS('SELECT DISTINCT(`id_ebay_profile`) FROM `'._DB_PREFIX_.'ebay_profile`');
            $id_ebay_profile = EbayOrder::getIdProfilebyIdOrder($id_order);
            $order = new Order($id_order);

            $ebay_profile = new EbayProfile($id_ebay_profile);
            if ($ebay_profile->getConfiguration('EBAY_SEND_TRACKING_CODE')) {
                $carrier = new Carrier($order->id_carrier, $ebay_profile->id_lang);
                $ebay_request = new EbayRequest($id_ebay_profile);
                $ebay_request->updateOrderTracking($id_order_ref, $tracking_number, $carrier->name);
            }
        }

        if (!((version_compare(_PS_VERSION_, '1.5.1', '>=')
            && version_compare(_PS_VERSION_, '1.5.2', '<'))
            && !Shop::isFeatureActive())) {
            $this->hookHeader($params);
        }
        $id_shop =  Shop::getContextShopID();
        $profiles = EbayProfile::getProfilesByIdShop($id_shop);
        $id_ebay_profiles = array();
        foreach ($profiles as &$profile) {
            $profile['site_name'] = 'ebay.'.EbayCountrySpec::getSiteExtensionBySiteId($profile['ebay_site_id']);
            $id_ebay_profiles[] = $profile['id_ebay_profile'];
        }
        if (Configuration::get('EBAY_VERSION') != $this->version) {
            set_time_limit(3600);
            //Configuration::set('EBAY_VERSION', $this->version);
            $this->setConfiguration('EBAY_VERSION', $this->version);
            Configuration::updateValue('EBAY_SEND_STATS', true);
            foreach ($profiles as &$profile) {
                $profile_ebay = new EbayProfile($profile['id_ebay_profile']);
                if (!$profile_ebay->getConfiguration('EBAY_PAYPAL_EMAIL')) {
                    $profile_ebay->setConfiguration('EBAY_PARAMETERS_TAB_OK', 0);
                } else {
                    $profile_ebay->setConfiguration('EBAY_PARAMETERS_TAB_OK', 1);
                    $profile_ebay->setConfiguration('EBAY_ANONNCES_CONFIG_TAB_OK', 1);
                    $profile_ebay->setConfiguration('EBAY_ORDERS_CONFIG_TAB_OK', 1);
                    $profile_ebay->setConfiguration('EBAY_SHIPPING_CONFIG_TAB_OK', 1);
                    $profile_ebay->setConfiguration('EBAY_ORDERS_DAYS_BACKWARD', 14);
                }
                if ($profile_ebay->getConfiguration('EBAY_SYNC_PRODUCTS_MODE') == "A") {
                    EbayCategoryConfiguration::activeAllCAtegories($profile['id_ebay_profile']);
                }
            }
            $validatordb = new EbayDbValidator();
            $validatordb->checkTemplate();
            $validatordb->checkDatabase(false);
        }

        if ($visible_logo){
            $nb_products = EbayProduct::getNbProductsByIdEbayProfiles($id_ebay_profiles);
            $nb_errors=0;
            foreach ($profiles as &$profile) {
                $profile_count_order_errors = EbayOrderErrors::getAllCount($profile['id_ebay_profile']);
                $profile_count_product_errors = EbayTaskManager::getErrorsCount($profile['id_ebay_profile']);
                $ebay_profile_hook = new EbayProfile($profile['id_ebay_profile']);
                $profile['nb_tasks'] = EbayTaskManager::getNbTasks($profile['id_ebay_profile']);
                $profile['count_order_errors'] = (isset($profile_count_order_errors['nb']) ? $profile_count_order_errors['nb']:0);
                $profile['count_product_errors'] = (isset($profile_count_product_errors['nb']) ?$profile_count_product_errors['nb']:0);
                $profile['nb_products'] = (isset($nb_products[$profile['id_ebay_profile']]) ? $nb_products[$profile['id_ebay_profile']] : 0);
                $profile['token'] = ($ebay_profile_hook->getToken()?1:0);
                $profile['category'] = (count(EbayCategoryConfiguration::getEbayCategories($profile['id_ebay_profile']))?1:0);
                $nb_errors +=$profile['count_product_errors'];
            }
        }

        $link = $this->context->link;
        if ($visible_logo){
            $smarty_vars = array(
                'visible_logo' => $visible_logo,
                'path' => $this->_path,
                'profiles' => $profiles,
                'nb_errors' => $nb_errors,
                'url_ebay' => $link->getAdminLink('AdminModules').'&configure=ebay&module_name',
                '_module_ebay_dir_' => _MODULE_DIR_,
                'ebay_token' =>  Configuration::get('EBAY_SECURITY_TOKEN'),
                'cron_url' => Tools::getShopDomainSsl(true).__PS_BASE_URI__.'modules/ebay/synchronizeProducts_CRON.php',
            );
        } else{
            $smarty_vars = array(
                'visible_logo' => $visible_logo,
                'path' => $this->_path,
                'url_ebay' => $link->getAdminLink('AdminModules').'&configure=ebay&module_name',
                '_module_ebay_dir_' => _MODULE_DIR_,
                'ebay_token' =>  Configuration::get('EBAY_SECURITY_TOKEN'),
                'cron_url' => Tools::getShopDomainSsl(true).__PS_BASE_URI__.'modules/ebay/synchronizeProducts_CRON.php',
            );
        }

        $this->smarty->assign($smarty_vars);
        return $this->display(__FILE__, 'views/templates/hook/header_info.tpl');
    }
    protected function _getModuleUrl()
    {
        return Tools::getShopDomain(true).__PS_BASE_URI__.'modules/ebay/';
    }
    /**
     * Main Form Method
     *
     */
    public function getContent()
    {
        $adminDir = basename(_PS_ADMIN_DIR_);
        $this->smarty->assign(array(
            'adminDir' => '/'.$adminDir,
        ));

        if (Configuration::get('EBAY_VERSION') != $this->version) {
            set_time_limit(3600);
            Configuration::set('EBAY_VERSION', $this->version);
            $this->setConfiguration('EBAY_VERSION', $this->version);
            $validatordb = new EbayDbValidator();
            $validatordb->checkTemplate();
            $validatordb->checkDatabase(false);
        }

        if (Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->bootstrap = true;
            return $this->display(__FILE__, 'views/templates/hook/alert_multishop.tpl');
        }

        if ($this->ebay_profile && !Configuration::get('EBAY_CATEGORY_MULTI_SKU_UPDATE')) {
            $ebay = new EbayRequest();
            EbayCategory::updateCategoryTable($ebay->getCategoriesSkuCompliancy());
        }

        if (Tools::getValue('refresh_store_cat')) {
            $ebay = new EbayRequest();
            EbayStoreCategory::updateStoreCategoryTable($ebay->getStoreCategories(), $this->ebay_profile);
        }



        if ($this->ebay_profile) {
            $this->ebay_profile->loadStoreCategories();
        }

        // Checking Extension
        if (!extension_loaded('curl') || !ini_get('allow_url_fopen')) {
            if (!extension_loaded('curl') && !ini_get('allow_url_fopen')) {
                return $this->html.$this->displayError($this->l('You must enable cURL extension and allow_url_fopen option on your server if you want to use this module.'));
            } elseif (!extension_loaded('curl')) {
                return $this->html.$this->displayError($this->l('You must enable cURL extension on your server if you want to use this module.'));
            } elseif (!ini_get('allow_url_fopen')) {
                return $this->html.$this->displayError($this->l('You must enable allow_url_fopen option on your server if you want to use this module.'));
            }
        }

        // If isset Post Var, post process else display form
        if (!empty($_POST) && (Tools::isSubmit('submitSave') || Tools::isSubmit('btnSubmitSyncAndPublish') || Tools::isSubmit('btnSubmitSync'))) {
            $errors = null;

            if (!count($errors)) {
                $this->__postProcess();
            } else {
                $vars = array(
                    'errors' => $errors
                );
                $this->html .= $this->display('alert_tabs.tpl', $vars);
            }

            if (Configuration::get('EBAY_SEND_STATS')) {
                $ebay_stat = new EbayStat($this->stats_version, $this->ebay_profile);
                $ebay_stat->save();
            }
        }
        $this->context->controller->addCSS(
            // $this->_path.'/views/css/ebayV2.css'
            $this->_path.'/views/css/admin.css'
        );
        $this->html .= $this->__displayForm();

        // Set old Context Shop
        /* RAPH
        if (version_compare(_PS_VERSION_, '1.5', '>') && Shop::isFeatureActive())
        $this->_setContextShop($old_context_shop);
         */

        return $this->html;
    }

    private function __displayForm()
    {
        // Save eBay Stats
        if (Tools::getIsset('stats')) {
            Configuration::updateValue('EBAY_SEND_STATS', Tools::getValue('stats') ? 1 : 0, false, 0, 0);
        }
        // End Save eBay Stats

        $alerts = $this->__getAlerts();

        $stream_context = @stream_context_create(array('http' => array('method' => 'GET', 'timeout' => 2)));
        $count_order_errors = 0;
        $count_product_errors = 0;
        if ($this->ebay_profile) {
            $count_order_errors = EbayOrderErrors::getAll($this->ebay_profile->id);
            $count_product_errors =EbayTaskManager::getErrors($this->ebay_profile->id);
        }

        $url_data = array(
            'version' => $this->version,
            'shop' => urlencode(Configuration::get('PS_SHOP_NAME')),
            'registered' => in_array('registration', $alerts) ? 'no' : 'yes',
            'url' => urlencode($_SERVER['HTTP_HOST']),
            'iso_country' => (Tools::strtolower($this->ebay_country->getIsoCode())),
            'iso_lang' => Tools::strtolower($this->context->language->iso_code),
            'id_lang' => (int) $this->context->language->id,
            'email' => urlencode(Configuration::get('PS_SHOP_EMAIL')),
            'security' => md5(Configuration::get('PS_SHOP_EMAIL')._COOKIE_IV_),

        );
        $url = 'http://api.prestashop.com/partner/modules/ebay.php?'.http_build_query($url_data);

        $prestashop_content = @Tools::file_get_contents($url, false, $stream_context);
        if (!Validate::isCleanHtml($prestashop_content)) {
            $prestashop_content = '';
        }

        $ebay_send_stats = Configuration::get('EBAY_SEND_STATS');

        // profiles data
        $id_shop =  Shop::getContextShopID();
        $profiles = EbayProfile::getProfilesByIdShop($id_shop);
        $id_ebay_profiles = array();
        foreach ($profiles as &$profile) {
            $profile['site_name'] = 'ebay.'.EbayCountrySpec::getSiteExtensionBySiteId($profile['ebay_site_id']);
            $id_ebay_profiles[] = $profile['id_ebay_profile'];
        }

        $nb_products = EbayProduct::getNbProductsByIdEbayProfiles($id_ebay_profiles);
        foreach ($profiles as &$profile) {
            $profile_count_order_errors = EbayOrderErrors::getAllCount($profile['id_ebay_profile']);
            $profile_count_product_errors = EbayTaskManager::getErrorsCount($profile['id_ebay_profile']);
            $ebay_profile_hook = new EbayProfile($profile['id_ebay_profile']);
            $profile['nb_tasks'] = EbayTaskManager::getNbTasks($profile['id_ebay_profile']);
            $profile['count_order_errors'] = (isset($profile_count_order_errors[0]['nb']) ? $profile_count_order_errors[0]['nb']:0);
            $profile['count_product_errors'] = (isset($profile_count_product_errors[0]['nb']) ?$profile_count_product_errors[0]['nb']:0);
            $profile['nb_products'] = (isset($nb_products[$profile['id_ebay_profile']]) ? $nb_products[$profile['id_ebay_profile']] : 0);
            $profile['token'] = ($ebay_profile_hook->getToken()?1:0);
            $profile['category'] = (count(EbayCategoryConfiguration::getEbayCategories($profile['id_ebay_profile']))?1:0);
        }

        $add_profile = (Tools::getValue('action') == 'addProfile');

        $url_vars = array(
            'id_tab' => '1',
            'section' => 'parameters',
            'action' => 'addProfile',
        );

        $url_vars['controller'] = Tools::getValue('controller');


        $add_profile_url = $this->__getUrl($url_vars);

        // main tab
        $id_tab = Tools::getValue('id_tab', 81);
        if (in_array($id_tab, array(6, 78))) {
            $main_tab = 'orders';
        } elseif (in_array($id_tab, array(5, 80, 9, 16, 106))) {
            $main_tab = 'annonces';
        } elseif (in_array($id_tab, array(1,101,102,3,103,4,13))) {
            $main_tab = 'settings';
        } else {
            $main_tab = 'dashbord';
        }
        $request = new EbayRequest();

        $id_profile = $this->ebay_profile && $this->ebay_profile->id ? $this->ebay_profile->id : '';
        if ($this->ebay_profile){
            $count_orphan = EbayProduct::getCountOrphanListing($this->ebay_profile->id);
            $count_orphan = (int) $count_orphan[0]['number'];
        } else{
            $count_orphan = 0;
        }



        $this->smarty->assign(array(
            'nb_tasks_in_work_url' => Tools::getShopDomainSsl(true).__PS_BASE_URI__.'modules/ebay/ajax/loadNbTasksInWork.php?token='.Configuration::get('EBAY_SECURITY_TOKEN').'&id_profile='.$id_profile,
            'img_stats' => ($this->ebay_country->getImgStats()),
            'alert' => $alerts,
            'regenerate_token' => Configuration::get('EBAY_TOKEN_REGENERATE', null, 0, 0),
            'prestashop_content' => $prestashop_content,
            'path' => $this->_path,
            'multishop' =>  Shop::isFeatureActive(),
            'site_extension' => ($this->ebay_country->getSiteExtension()),
            'documentation_lang' => ($this->ebay_country->getDocumentationLang()),
            'is_version_one_dot_five_dot_one' => (version_compare(_PS_VERSION_, '1.5.1', '>=') && version_compare(_PS_VERSION_, '1.5.2', '<')),
            'css_file' => $this->_path.'views/css/ebay_back.css',
            'font_awesome_css_file' => $this->_path.'views/css/font-awesome.min.css',
            'tooltip' => $this->_path.'views/js/jquery.tooltipster.min.js',
            'tips202' => $this->_path.'views/js/202tips.js',
            'noConflicts' => $this->_path.'views/js/jquery.noConflict.php?version=1.7.2',
            'ebayjquery' => $this->_path.'views/js/jquery-1.7.2.min.js',
            'fancybox' => $this->_path.'views/js/jquery.fancybox.min.js',
            'fancyboxCss' => $this->_path.'views/css/jquery.fancybox.css',
            'parametersValidator' => ($this->ebay_profile ? EbayValidatorTab::getParametersTabConfiguration($this->ebay_profile->id) : ''),
            'categoryValidator' => ($this->ebay_profile ? EbayValidatorTab::getCategoryTabConfiguration($this->ebay_profile->id) : ''),
            'itemSpecificValidator' => ($this->ebay_profile ? EbayValidatorTab::getItemSpecificsTabConfiguration($this->ebay_profile->id) : ''),
            'shippingValidator' => ($this->ebay_profile ? EbayValidatorTab::getShippingTabConfiguration($this->ebay_profile->id) : ''),
            'synchronisationValidator' => ($this->ebay_profile ? EbayValidatorTab::getSynchronisationTabConfiguration($this->ebay_profile->id) : ''),
            'templateValidator' => ($this->ebay_profile ? EbayValidatorTab::getTemplateTabConfiguration($this->ebay_profile->id) : ''),
            'businesspoliciesValidator' => ($this->ebay_profile ? EbayValidatorTab::getEbayBusinessPoliciesTabConfiguration($this->ebay_profile->id) : ''),
            'show_welcome_stats' => $ebay_send_stats === false,
            'free_shop_for_90_days' => $this->shopIsAvailableFor90DaysOffer(),
            'show_welcome' => (($ebay_send_stats !== false) && (!count($id_ebay_profiles))),
            'show_seller_tips' => ( $this->ebay_profile && $this->ebay_profile->getToken()),
            'current_profile' => $this->ebay_profile,
            'current_profile_site_extension' => ($this->ebay_profile ? EbayCountrySpec::getSiteExtensionBySiteId($this->ebay_profile->ebay_site_id) : ''),
            'profiles' => $profiles,
            'add_profile' => $add_profile,
            'add_profile_url' => $add_profile_url,
            'delete_profile_url' => Tools::getShopDomainSsl(true).__PS_BASE_URI__.'ebay/ajax/deleteProfile.php?token='.Configuration::get('EBAY_SECURITY_TOKEN').'&time='.pSQL(date('Ymdhis')),
            'main_tab' => $main_tab,
            'id_tab' => (int) Tools::getValue('id_tab'),
            'pro_url' => $this->ebay_country->getProUrl(),
            'signin_pro_url' => $this->ebay_country->getSignInProURL(),
            'fee_url' => $this->ebay_country->getFeeUrl(),
            'title_desc_url' => $this->ebay_country->getTitleDescUrl(),
            'picture_url' => $this->ebay_country->getPictureUrl(),
            'similar_items_url' => $this->ebay_country->getSimilarItemsUrl(),
            'top_rated_url' => $this->ebay_country->getTopRatedUrl(),
            '_module_dir_' => _MODULE_DIR_,
            'date' => pSQL(date('Ymdhis')),
            'debug' => ($request->getDev())? 1:0,
            'id_lang' => ($this->ebay_profile && $this->ebay_profile->id_lang?$this->ebay_profile->id_lang:Configuration::get('PS_LANG_DEFAULT')),
            'id_ebay_profile' => ($this->ebay_profile && $this->ebay_profile->id ? $this->ebay_profile->id : false),
            'count_order_errors' => ($count_order_errors?count($count_order_errors):0),
            'count_product_errors' => ($count_product_errors?count($count_product_errors):0),
            'count_product_errors_total' => ($count_product_errors?count($count_product_errors):0)+ $count_orphan,
            'ebay_shipping_config_tab' => ($this->ebay_profile?$this->ebay_profile->getConfiguration('EBAY_SHIPPING_CONFIG_TAB_OK'):0),
            'count_category' => ($this->ebay_profile?EbayCategoryConfiguration::getNbPrestashopCategories($this->ebay_profile->id):0),
            'help_listing_rejection' => array(
                'lang'           => $this->context->country->iso_code,
                'module_version' => $this->version,
                'prestashop_version'     => _PS_VERSION_,
                'errorcode'     => 'HELP_LISTING_REJECTION',
            ),
            'help_order_import' => array(
                'lang'           => $this->context->country->iso_code,
                'module_version' => $this->version,
                'prestashop_version'     => _PS_VERSION_,
                'errorcode'     => 'HELP_ORDER_IMPORT',
            ),
            'help_ebay_seller' => array(
                'lang'           => $this->context->country->iso_code,
                'module_version' => $this->version,
                'prestashop_version'     => _PS_VERSION_,
                'errorcode'     => 'HELP_EBAY_SELLER_CONTACT',
            ),

            ));

        // test if multishop Screen and all shops

        $is_all_shops = in_array(Shop::getContext(), array(Shop::CONTEXT_ALL, Shop::CONTEXT_GROUP));


        if (!($this->ebay_profile && $this->ebay_profile->getToken()) || $add_profile || Tools::isSubmit('ebayRegisterButton')) {
            $template = $this->__displayFormRegister();
        } else {
            $template = $this->__displayFormConfig();
        }
        return $this->display(__FILE__, 'views/templates/hook/form.tpl').$template;
    }

    private function shopIsAvailableFor90DaysOffer()
    {
        $ebay_site_offers = array(
            'FR',
            'IT',
            'ES',
        );
        $country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
        if (in_array($country->iso_code, $ebay_site_offers)) {
            return true;
        }
        return false;
    }

    private function __postValidation()
    {
        if (Tools::getValue('section') != 'parameters') {
            return null;
        }

        $errors = array();

        if (!Validate::isEmail(Tools::getValue('ebay_paypal_email'))) {
            $errors[] = $this->l('Your PayPal email address is not specified or invalid');
        }

        if (!Tools::getValue('ebay_shop_postalcode') || !Validate::isPostCode(Tools::getValue('ebay_shop_postalcode'))) {
            $errors[] = $this->l('Your shop\'s postal code is not specified or is invalid');
        }

        return $errors;
    }

    private function __postProcess()
    {
        if (Tools::getValue('section') == '') {
            $this->__postProcessStats();
        }

        if (Tools::getValue('section') == 'parameters') {
            $tab = new EbayFormParametersTab($this, $this->smarty, $this->context);
        } elseif (Tools::getValue('section') == 'parametersannonces') {
            $tab = new EbayFormConfigAnnoncesTab($this, $this->smarty, $this->context);
        } elseif (Tools::getValue('section') == 'parametersorders') {
            $tab = new EbayFormConfigOrdersTab($this, $this->smarty, $this->context);
        } elseif (Tools::getValue('section') == 'shipping') {
            $tab = new EbayFormShippingTab($this, $this->smarty, $this->context);
        } elseif (Tools::getValue('section') == 'intershipping') {
            $tab = new EbayFormInterShippingTab($this, $this->smarty, $this->context);
        } elseif (Tools::getValue('section') == 'template') {
            $tab = new EbayFormTemplateManagerTab($this, $this->smarty, $this->context);
        } elseif (Tools::getValue('section') == 'sync') {
            $tab = new EbayFormEbaySyncTab($this, $this->smarty, $this->context);
        } elseif (Tools::getValue('section') == 'advanced_parameters') {
            $tab = new EbayFormAdvancedParametersTab($this, $this->smarty, $this->context);
        }

        if (isset($tab)) {
            $this->html .= $tab->postProcess();
        }
    }

    /**
     * Form Config Methods
     *
     **/
    private function __displayFormStats()
    {
        $smarty_vars = array(
        );

        $this->smarty->assign($smarty_vars);

        return $this->display(__FILE__, 'views/templates/hook/formStats.tpl');
    }

    /**
     * Register Form Config Methods
     **/
    private function __displayFormRegister()
    {
        $ebay = new EbayRequest();
        self::addSmartyModifiers();
        $smarty_vars = array();
        $smarty_vars['show_send_stats'] = Configuration::get('EBAY_SEND_STATS') === false ? true : false;

        if (Tools::getValue('relogin')) {
            $session_id = $ebay->login();
            $this->context->cookie->eBaySession = $session_id;
            Configuration::updateValue('EBAY_API_SESSION', $session_id, false, 0, 0);

            $smarty_vars = array_merge($smarty_vars, array(
                'relogin' => true,
                'redirect_url' => $ebay->getLoginUrl().'?SignIn&runame='.$ebay->runame.'&SessID='.$this->context->cookie->eBaySession,
            ));
        } else {
            $smarty_vars['relogin'] = false;
        }

        $logged = (!empty($this->context->cookie->eBaySession) && Tools::getValue('action') == 'logged');
        $smarty_vars['logged'] = $logged;

        $id_shop =  Shop::getContextShopID();
        if ($logged) {
            if ($ebay_username = Tools::getValue('eBayUsernamesList')) {
                if ($ebay_username == -1) {
                    $ebay_username = Tools::getValue('eBayUsername');
                }

                $this->context->cookie->eBayUsername = $ebay_username;

                $this->ebay_profile = EbayProfile::getByLangShopSiteAndUsername((int) Tools::getValue('ebay_language'), $id_shop, Tools::getValue('ebay_country'), $ebay_username, EbayProductTemplate::getContent($this, $this->smarty));
                EbayProfile::setProfile($this->ebay_profile->id);
            }

            $smarty_vars['check_token_tpl'] = $this->displayCheckToken();
        } else {
            // not logged yet
            if (empty($this->context->cookie->eBaySession)) {
                $session_id = $ebay->login();
                $this->context->cookie->eBaySession = $session_id;
                Configuration::updateValue('EBAY_API_SESSION', $session_id, false, 0, 0);
                $this->context->cookie->write();
            }

            if (isset($this->ebay_profile->id_shop)) {
                $ebay_profiles = EbayProfile::getProfilesByIdShop($this->ebay_profile->id_shop);
            } else {
                $ebay_profiles = array();
            }

            foreach ($ebay_profiles as &$profile) {
                $profile['site_extension'] = EbayCountrySpec::getSiteExtensionBySiteId($profile['ebay_site_id']);
            }

            $smarty_vars = array_merge($smarty_vars, array(
                'action_url' => $_SERVER['REQUEST_URI'].'&action=logged',
                'ebay_username' => $this->context->cookie->eBayUsername,
                'window_open_url' => '?SignIn&runame='.$ebay->runame.'&SessID='.$this->context->cookie->eBaySession,
                'ebay_countries' => EbayCountrySpec::getCountries($ebay->getDev()),
                'default_country' => EbayCountrySpec::getKeyForEbayCountry(),
                'ebay_user_identifiers' => EbayProfile::getEbayUserIdentifiers(),
                'ebay_profiles' => $ebay_profiles,
                'languages' => Language::getLanguages(true, ($this->ebay_profile ? $this->ebay_profile->id_shop : $id_shop)),
            ));
        }

        $this->smarty->assign($smarty_vars);

        return $this->display(__FILE__, 'views/templates/hook/formRegister.tpl');
    }

    /**
     *
     * Waiting screen when expecting eBay login to refresh the token
     *
     */

    public function displayCheckToken()
    {
        $url_vars = array(
            'action' => 'validateToken',
            'path' => $this->_path,
        );

        $url_vars['controller'] = Tools::getValue('controller');


        $url = _MODULE_DIR_.'ebay/ajax/checkToken.php?'.http_build_query(
            array(
                'token' => Configuration::get('EBAY_SECURITY_TOKEN'),
                'time' => pSQL(date('Ymdhis')),
            )
        );

        $smarty_vars = array(
            'window_location_href' => $this->__getUrl($url_vars),
            'url' => $url,
            'request_uri' => $_SERVER['REQUEST_URI'],
        );

        $this->smarty->assign($smarty_vars);

        return $this->display(__FILE__, 'views/templates/hook/checkToken.tpl');
    }

    /**
     * Form Config Methods
     *
     **/
    private function __displayFormConfig()
    {

        self::addSmartyModifiers();
        $form_parameters_tab = new EbayFormParametersTab($this, $this->smarty, $this->context);
        $form_advanced_parameters_tab = new EbayFormAdvancedParametersTab($this, $this->smarty, $this->context);

        $form_shipping_tab = new EbayFormShippingTab($this, $this->smarty, $this->context);
        $form_inter_shipping_tab = new EbayFormInterShippingTab($this, $this->smarty, $this->context);
        $form_template_manager_tab = new EbayFormTemplateManagerTab($this, $this->smarty, $this->context);
        //$form_ebay_sync_tab = new EbayFormEbaySyncTab($this, $this->smarty, $this->context);
        //$help_tab = new EbayHelpTab($this, $this->smarty, $this->context);
        $listings_tab = new EbayListingsTab($this, $this->smarty, $this->context);
        $orders_sync = new EbayOrdersSyncTab($this, $this->smarty, $this->context);
        $ps_products = new EbayPrestashopProductsTab($this, $this->smarty, $this->context);
        $orphan_listings = new EbayOrphanListingsTab($this, $this->smarty, $this->context);
        $tableOrders = new EbayOrdersTab($this, $this->smarty, $this->context);
        $tableListErrorProduct = new EbayListErrorsProductsTab($this, $this->smarty, $this->context);
        $form_parameters_annonces_tab = new EbayFormConfigAnnoncesTab($this, $this->smarty, $this->context);
        $form_parameters_orders_tab = new EbayFormConfigOrdersTab($this, $this->smarty, $this->context);
        $ebayProductsExcluTab = new EbayProductsExcluTab($this, $this->smarty, $this->context);
        $dashboard = new EbayDashboardTab($this, $this->smarty, $this->context, $this->_path);

        $order_logs = new EbayOrderLogsTab($this, $this->smarty, $this->context, $this->_path);
        $order_returns = new EbayOrderReturnsTab($this, $this->smarty, $this->context, $this->_path);
        $orders_returns_sync = new EbayOrdersReturnsSyncTab($this, $this->smarty, $this->context);

        $log_jobs = new EbayLogJobsTab($this, $this->smarty, $this->context);
        $log_workers = new EbayLogWorkersTab($this, $this->smarty, $this->context);
        // test if everything is green
        if ($this->ebay_profile && $this->ebay_profile->isAllSettingsConfigured()) {
            if (!$this->ebay_profile->getConfiguration('EBAY_HAS_SYNCED_PRODUCTS')) {
               $green_message = $this->l('Your profile is ready to go, go to Synchronization to list your products');
            } elseif (!empty($_POST) && Tools::isSubmit('submitSave')) {
// config has changed
                $green_message = $this->l('To implement these changes on active listings you need to    resynchronize your items');
            }
        }

        // Get all alerts
        $alert = new EbayAlert($this);
        
        /*if ($this->ebay_profile->getConfiguration('EBAY_LAST_ALERT_MAIL') === null
            || $this->ebay_profile->getConfiguration('EBAY_LAST_ALERT_MAIL') < date('Y-m-d\TH:i:s', strtotime('-1 day')).'.000Z'
        ) {
            $alert->sendDailyMail();
            $this->ebay_profile->setConfiguration('EBAY_LAST_ALERT_MAIL', date('Y-m-d\TH:i:s').'.000Z');
        }*/
        $count_order_errors = 0;
        $count_product_errors = 0;
        $count_orphan_listing = 0;
        if ($this->ebay_profile) {
            $count_order_errors = EbayOrderErrors::getAllCount($this->ebay_profile->id);
            $count_product_errors = EbayTaskManager::getErrorsCount($this->ebay_profile->id);
            $count_orphan_listing = EbayProduct::getCountOrphanListing($this->ebay_profile->id);
            $count_orphan_listing = $count_orphan_listing[0]['number'];
        }

        $smarty_vars = array(
            'class_general' => 'uncinq',
            'form_parameters' => $form_parameters_tab->getContent(),
            'form_advanced_parameters' => $form_advanced_parameters_tab->getContent(),
            'form_shipping' => $form_shipping_tab->getContent(),
            'form_inter_shipping' => $form_inter_shipping_tab->getContent(),
            'form_template_manager' => $form_template_manager_tab->getContent(),
            //'form_ebay_sync' => $form_ebay_sync_tab->getContent(),
            'ebay_listings' => $listings_tab->getContent($this->ebay_profile->id),
            'orders_sync' => $orders_sync->getContent(),
            'ps_products' => $ps_products->getContent(),
            'orphan_listings' => $orphan_listings->getContent(),
            'green_message' => isset($green_message) ? $green_message : null,
            'order_logs' => $order_logs->getContent(),
            'id_tab' => Tools::getValue('id_tab'),
            'alerts' => $alert->getAlerts(),
            'ps_version' => _PS_VERSION_,
            'admin_path' => basename(_PS_ADMIN_DIR_),
            'load_kb_path' => _MODULE_DIR_.'ebay/ajax/loadKB.php',
            'alert_exit_import_categories' => $this->l('Import of eBay category is running.'),
            'order_returns' => $order_returns->getContent($this->ebay_profile->id),
            'orders_returns_sync' => $orders_returns_sync->getContent(),
            'dashboard' =>   $dashboard->getContent($this->ebay_profile->id),
            'table_orders' => $tableOrders ->getContent($this->ebay_profile->id),
            'table_product_error'=> $tableListErrorProduct->getContent($this->ebay_profile->id),
            'count_order_errors' => (isset($count_order_errors[0]['nb'])?$count_order_errors[0]['nb']:0),
            'count_product_errors' => (isset($count_product_errors[0]['nb'])?$count_product_errors[0]['nb']:0),
            'count_product_errors_total' => (isset($count_product_errors[0]['nb'])?$count_product_errors[0]['nb']:0)+ ($count_orphan_listing?$count_orphan_listing:0),
            'count_orphan_listing' => ($count_orphan_listing?$count_orphan_listing:0),
            'form_parameters_annonces_tab' => $form_parameters_annonces_tab->getContent(),
            'form_parameters_orders_tab' => $form_parameters_orders_tab->getContent(),
            'ebayProductsExcluTab' => $ebayProductsExcluTab->getContent($this->ebay_profile),
            'ebayLogJobs' => $log_jobs->getContent($this->ebay_profile->id),
            'ebayLogWorkers' => $log_workers->getContent($this->ebay_profile->id),
            );


        $this->smarty->assign($smarty_vars);

        return $this->display(__FILE__, 'views/templates/hook/formConfig.tpl');
    }

    public function login()
    {
        $ebay = new EbayRequest();

        $session_id = $ebay->login();
        $this->context->cookie->eBaySession = $session_id;
        Configuration::updateValue('EBAY_API_SESSION', $session_id, false, 0, 0);

        return $session_id;
    }

    private function __postProcessAddProfile()
    {
        $ebay_username = Tools::getValue('eBayUsernamesList');
        if (!$ebay_username || ($ebay_username == -1)) {
            $ebay_username = Tools::getValue('eBayUsername');
        }

        if ($ebay_username) {
            $this->context->cookie->eBayUsername = $ebay_username;

            $id_shop =  Shop::getContextShopID();

            $this->ebay_profile = EbayProfile::getByLangShopSiteAndUsername((int) Tools::getValue('ebay_language'), $id_shop, Tools::getValue('ebay_country'), $ebay_username, EbayProductTemplate::getContent($this, $this->smarty));
            EbayProfile::setProfile($this->ebay_profile->id);
        }
    }

    private function __postProcessConfig()
    {
        if ($id_ebay_profile = (int) Tools::getValue('ebay_profile')) {
            if (!EbayProfile::setProfile($id_ebay_profile)) {
                $this->html .= $this->displayError($this->l('Profile cannot be changed'));
            }
        }
    }

    private function __postProcessStats()
    {
        if (Configuration::updateValue('EBAY_SEND_STATS', Tools::getValue('stats') ? 1 : 0, false, 0, 0)) {
            $this->html .= $this->displayConfirmation($this->l('Settings updated'));
        } else {
            $this->html .= $this->displayError($this->l('Settings failed'));
        }
    }

    /**
     * Category Form Config Methods
     * @param array  $categories
     * @param int    $id
     * @param array  $path
     * @param string $path_add
     * @param string $search
     * @return array
     */
    public function getChildCategories($categories, $id, $path = array(), $path_add = '', $search = '')
    {
        $category_tab = array();

        if ($path_add != '') {
            $path[] = $path_add;
        }

        if (isset($categories[$id])) {
            $cats = $categories[$id];
        } elseif (!$id) {
            $cats = reset($categories); // fix to deal with the case where the first element of categories has no key
        }
        if (isset($cats) && $cats) {
            foreach ($cats as $idc => $cc) {
                $name = '';
                if ($path) {
                    foreach ($path as $p) {
                        $name .= $p.' > ';
                    }
                }

                $name .= $cc['infos']['name'];
                $category_tab[] = array(
                    'id_category' => $cc['infos']['id_category'],
                    'name' => $name,
                    'active' => $cc['infos']['active'],
                );

                $categoryTmp = $this->getChildCategories($categories, $idc, $path, $cc['infos']['name'], '');

                $category_tab = array_merge($category_tab, $categoryTmp);
            }
        }
        if ($search) {
            $category_tab_filtered = array();
            foreach ($category_tab as $c) {
                if (strpos(Tools::strtolower($c['name']), Tools::strtolower($search)) !== false) {
                    $category_tab_filtered[] = $c;
                }
            }
            $category_tab = $category_tab_filtered;
        }

        return $category_tab;
    }



    private function __relistItems()
    {
        if ($this->ebay_profile->getConfiguration('EBAY_LISTING_DURATION') != 'GTC'
            && $this->ebay_profile->getConfiguration('EBAY_AUTOMATICALLY_RELIST') == 'on') {
            //We do relist automatically each day
            $this->ebay_profile->setConfiguration('EBAY_LAST_RELIST', date('Y-m-d'));

            $ebay = new EbayRequest();
            $days = Tools::substr($this->ebay_profile->getConfiguration('EBAY_LISTING_DURATION'), 5);

            foreach (EbayProduct::getProducts($days, 10) as $item) {
                $new_item_id = $ebay->relistFixedPriceItem($item['id_product_ref']);

                if (!$new_item_id) {
                    $new_item_id = $item['id_product_ref'];
                }

                //Update of the product so that we don't take it in the next 10 products to relist !
                EbayProduct::updateByIdProductRef($item['id_product_ref'], array(
                    'id_product_ref' => pSQL($new_item_id),
                    'date_upd' => date('Y-m-d h:i:s')));
            }
        }
    }

    private function __getAlerts()
    {
        $alerts = array();

        if ($this->ebay_profile && !$this->ebay_profile->getToken()) {
            $alerts[] = 'registration';
        }

        if (!ini_get('allow_url_fopen')) {
            $alerts[] = 'allowurlfopen';
        }

        if (!extension_loaded('curl')) {
            $alerts[] = 'curl';
        }

        if (!$this->ebay_profile) {
            return $alerts;
        }

        $ebay = new EbayRequest();
        //$user_profile = $ebay->getUserProfile(Configuration::get('EBAY_API_USERNAME', null, 0, 0));
        $user_profile = $ebay->getUserProfile($this->ebay_profile->ebay_user_identifier);

        $this->StoreName = $user_profile['StoreName'];

        if ($user_profile['SellerBusinessType'][0] != 'Commercial') {
            $alerts[] = 'SellerBusinessType';
        }

        return $alerts;
    }

    public function setConfiguration($config_name, $config_value, $html = false)
    {
        return Configuration::updateValue($config_name, $config_value, $html, 0, 0);
    }

    private function __getContextShop()
    {
        switch ($context_type = Shop::getContext()) {
            case Shop::CONTEXT_SHOP:
                $context_id = Shop::getContextShopID();
                break;
            case Shop::CONTEXT_GROUP:
                $context_id = Shop::getContextShopGroupID();
                break;
        }

        return array(
            $context_type,
            isset($context_id) ? $context_id : null,
        );
    }

    private function __getUrl($extra_vars = array())
    {
        $url_vars = array(
            'configure' => Tools::getValue('configure'),
            'token' => Tools::getValue('token'),
            'tab_module' => Tools::getValue('tab_module'),
            'module_name' => Tools::getValue('module_name'),
        );

        return 'index.php?'.http_build_query(array_merge($url_vars, $extra_vars));
    }

    /**
     * $newContextShop = array
     * @param int $type Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP | Shop::CONTEXT_SHOP
     * @param int $id ID shop if CONTEXT_SHOP or id shop group if CONTEXT_GROUP
     *
     **/
    private function __setContextShop($new_context_shop = null)
    {
        if ($new_context_shop) {
            Shop::setContext($new_context_shop[0], $new_context_shop[1]);
        } else {
            Shop::setContext(Shop::CONTEXT_SHOP, Configuration::get('PS_SHOP_DEFAULT'));
        }
    }

    public function addSqlRestrictionOnLang($alias)
    {

        return Shop::addSqlRestrictionOnLang($alias);
    }

    /**
     * used by loadTableCategories
     *
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * used by loadTableCategories & suggestCategories
     *
     */
    public function getContext()
    {
        return $this->context;
    }

    public function ajaxPreviewTemplate($content, $id_lang)
    {
        // random product
        $category = Category::getRootCategory($id_lang);
        $product = $category->getProducts($id_lang, 0, 1, null, null, false, true, true, 1, false);
        $product = $product[0];

        // data
        $data = array(
            'price' => $product['price'],
            'price_without_reduction' => '',
            'reduction' => $product['reduction'],
            'name' => $product['name'],
            'description' => $product['description'],
            'description_short' => $product['description_short'],
        );
        if ($data['reduction'] > 0) {
            $data['price_without_reduction'] = $product['price_without_reduction'];
        }

        // pictures product
        $product = new Product($product['id_product'], false, $id_lang);
        $pictures = EbaySynchronizer::__getPictures($product, $this->ebay_profile, $id_lang, $this->context, array());
        $data['large_pictures'] = $pictures['large'];
        $data['medium_pictures'] = $pictures['medium'];

        // features product
        $features_html = '';
        foreach ($product->getFrontFeatures($id_lang) as $feature) {
            $features_html .= '<b>'.$feature['name'].'</b> : '.$feature['value'].'<br/>';
        }

        $data['features'] = $features_html;

        $content = EbaySynchronizer::fillAllTemplate($data, $content);

        echo $content;
    }

    public function displayProductExclu($id_employee)
    {
        $ids_products = EbayProduct::getProductsBlocked($this->ebay_profile->id);
        $products = array();
        if (!empty($ids_products)) {
            foreach ($ids_products as $product_id) {
                $product = new Product($product_id['id_product'], false, $this->ebay_profile->id_lang);
                $ebay_category_id = EbayCategoryConfiguration::getConfigByCategoryId($this->ebay_profile->id, $product->id_category_default);
                if ($ebay_category_id) {
                    $ebay_category = EbayCategoryConfiguration::getEbayCategoryById($this->ebay_profile->id, $ebay_category_id[0]['id_ebay_category']);
                }
                $category_ps = new Category($product->id_category_default, $this->ebay_profile->id_lang);
                $products[] = array(
                    'id_product' => $product_id['id_product'],
                    'name' => $product->name,
                    'category_ebay' => (isset($ebay_category[0]['name'])?$ebay_category[0]['name']:''),
                    'category_ps' => $category_ps->name,
                );
            }
        }

        $vars = array(
            'products' => $products,
            'id_ebay_profile' => $this->ebay_profile->id,
            'ebay_token' => Configuration::get('EBAY_SECURITY_TOKEN'),
            'id_employee' => $id_employee,
        );
        $this->smarty->assign($vars);
        return $this->display(__FILE__, 'views/templates/hook/tableProductsExclu.tpl');
    }

    public function displayEbayListingsAjax($admin_path, $id_employee = null, $page_current=1, $length=20)
    {
        $ebay = new EbayRequest();
        $employee = new Employee($id_employee);
        $this->context->employee = $employee;
        $link = $this->context->link;
        $id_lang = $this->ebay_profile->id_lang;

        $products_ebay_listings = array();
        $nb_products = EbayProduct::getCountProductsWithoutBlacklisted($id_lang, $this->ebay_profile->id, false);
        $products = EbayProduct::getProductsWithoutBlacklisted($id_lang, $this->ebay_profile->id, false, $page_current);
        $pages_all = ceil(((int) $nb_products[0]['count'])/ (int) $length);
        $range =3;
        $start = $page_current - $range;
        if ($start <= 0)
            $start = 1;
        $stop = $page_current + $range;
        if ($stop>$pages_all)
            $stop = $pages_all;
        $prev_page = (int) $page_current - 1;
        $next_page = (int) $page_current + 1;
        $data = array(
            'id_lang' => $id_lang,
            'titleTemplate' => $this->ebay_profile->getConfiguration('EBAY_PRODUCT_TEMPLATE_TITLE'),
        );

        if ($this->isSymfonyProject()) {
            require_once _PS_MODULE_DIR_.'/../app/AppKernel.php';
            $kernel = new AppKernel(_PS_MODE_DEV_?'dev':'prod', _PS_MODE_DEV_);
            $kernel->loadClassCache();
            $kernel->boot();
            $router = $kernel->getContainer()->get('router');
        }

        while ($p = $products->fetch(PDO::FETCH_ASSOC)) {
            $data['real_id_product'] = (int) $p['id_product'];
            $data['name'] = $p['name'];
            $data['manufacturer_name'] = $p['manufacturer_name'];
            $data['reference'] = $p['reference'];
            $data['ean13'] = $p['ean13'];
            $data['upc'] = $p['upc'];
            $reference_ebay = $p['id_product_ref'];
            $product = new Product((int) $p['id_product'], true, $id_lang);
            $category= new Category($product->getDefaultCategory(), $id_lang);
            if ((int) $p['id_attribute'] > 0) {
                // No Multi Sku case so we do multiple products from a multivariation product
                $combinaison = $this->__getAttributeCombinationsById($product, (int) $p['id_attribute'], $id_lang);
                $combinaison = $combinaison[0];

                $data['reference'] = $combinaison['reference'];
                $data['ean13'] = $combinaison['ean13'];
                $data['upc'] = $combinaison['upc'];
                $variation_specifics = EbaySynchronizer::__getVariationSpecifics($combinaison['id_product'], $combinaison['id_product_attribute'], $id_lang, $this->ebay_profile->ebay_site_id);
                foreach ($variation_specifics as $variation_specific) {
                    $data['name'] .= ' '.$variation_specific;
                }

                if ($this->isSymfonyProject()) {
                    $url = $link->getAdminLink('AdminProducts')."/".$admin_path.$router->generate('admin_product_form', array('id' => $combinaison['id_product']));
                } else {
                    $url = method_exists($link, 'getAdminLink') ? $link->getAdminLink('AdminProducts').'&id_product='.(int) $combinaison['id_product'].'&updateproduct' : $link->getProductLink((int) $combinaison['id_product']);
                }

                $products_ebay_listings[] = array(
                    'id_product' => $combinaison['id_product'].'-'.$combinaison['id_product_attribute'],
                    'quantity' => $combinaison['quantity'],
                    'category' => $category->name,
                    'prestashop_title' => $data['name'],
                    'ebay_title' => EbayRequest::prepareTitle($data),
                    'reference_ebay' => $reference_ebay,
                    'link' => $url,
                    'link_ebay' => EbayProduct::getEbayUrl($reference_ebay, $ebay->getDev()),
                );
            } else {
                if ($this->isSymfonyProject()) {
                    $url = $link->getAdminLink('AdminProducts')."/".$admin_path.$router->generate('admin_product_form', array('id' => $data['real_id_product']));
                } else {
                    $url = method_exists($link, 'getAdminLink') ? $link->getAdminLink('AdminProducts').'&id_product='.(int) $data['real_id_product'].'&updateproduct' : $link->getProductLink((int) $data['real_id_product']);
                }

                $products_ebay_listings[] = array(
                    'id_product' => $data['real_id_product'],
                    'quantity' => $product->quantity,
                    'category' => $category->name,
                    'prestashop_title' => $data['name'],
                    'ebay_title' => EbayRequest::prepareTitle($data),
                    'reference_ebay' => $reference_ebay,
                    'link' => $url,
                    'link_ebay' => EbayProduct::getEbayUrl($reference_ebay, $ebay->getDev()),
                );
            }
        }
        $tpl_include = _PS_MODULE_DIR_.'ebay/views/templates/hook/pagination.tpl';
        $this->smarty->assign('products_ebay_listings', $products_ebay_listings);
        $this->smarty->assign('id_employee', $id_employee);
        $this->smarty->assign('admin_path', $admin_path);
        $this->smarty->assign(array(
            'prev_page'               => $prev_page,
            'next_page'               => $next_page,
            'tpl_include'             => $tpl_include,
            'pages_all'               => $pages_all,
            'page_current'            => $page_current,
            'start'                   => $start,
            'stop'                    => $stop,
        ));
        echo $this->display(__FILE__, 'views/templates/hook/ebay_listings_ajax.tpl');
    }

    /*
    public function displayWarning($msg)
    {
    if (method_exists($this, 'adminDisplayWarning'))
    return $this->adminDisplayWarning($msg);
    else
    return $this->context->tab->displayWarning($msg);
    }
     */

    public function _getAttributeCombinationsByIds($product, $id_attribute, $id_lang)
    {
        return $this->__getAttributeCombinationsById($product, $id_attribute, $id_lang);
    }

    private function __getAttributeCombinationsById($product, $id_attribute, $id_lang)
    {
        if (method_exists($product, 'getAttributeCombinationsById')) {

            return $product->getAttributeCombinationsById((int) $id_attribute, $id_lang);
        }

        $sql = 'SELECT pa.*, pa.`quantity`, ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name,
                    a.`id_attribute`, pa.`unit_price_impact`
                FROM `'._DB_PREFIX_.'product_attribute` pa
                LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int) $id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int) $id_lang.')
                WHERE pa.`id_product` = '.(int) $product->id.'
                AND pa.`id_product_attribute` = '.(int) $id_attribute.'
                GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                ORDER BY pa.`id_product_attribute`';
		
        return Db::getInstance()->ExecuteS($sql);
    }


    ############################################################################################################
    # Logger // Debug
    ############################################################################################################

    /**
     * Fonction de log
     *
     * Enregistre dans /modules/ebay/log/debug.log
     *
     * @param     $object
     * @param int $error_level
     */
    public static function debug($object, $error_level = 0)
    {
        $error_type = array(
            0 => "[ALL]",
            1 => "[DEBUG]",
            2 => "[INFO]",
            3 => "[WARN]",
            4 => "[ERROR]",
            5 => "[FATAL]"
        );
        $module_name = "ebay";
        $backtrace = debug_backtrace();
        $date = date("<Y-m-d(H:i:s)>");
        $file = $backtrace[0]['file'].":".$backtrace[0]['line'];
        $stderr = fopen(_PS_MODULE_DIR_.'/'.$module_name.'/log/debug.log', 'a');
        fwrite($stderr, $error_type[$error_level]." ".$date." ".$file."\n".print_r($object, true)."\n\n");
        fclose($stderr);
    }

    /**
     * Allow us to use last features from SF component
     * @return bool
     */
    public function isSymfonyProject()
    {
        return (bool) version_compare(_PS_VERSION_, '1.7', '>=');
    }
}
