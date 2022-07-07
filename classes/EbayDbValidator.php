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

class EbayDbValidator
{
    private $logs;
    // LOG : array(
    // 'table' => array('status' => 'success', 'action' => 'ALTER TABLE', 'result' => 'GOOD')
    // );

    private $database = [
        'ebay_api_log' => [
            'id_ebay_api_log' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11, 'null' => true, 'unsigned' => true, 'default' => null],
            'type' => ['type' => 'varchar', 'length' => 40],
            'context' => ['type' => 'varchar', 'length' => 40],
            'data_sent' => ['type' => 'text', 'length' => null],
            'response' => ['type' => 'text', 'length' => null],
            'request' => ['type' => 'text', 'length' => null],
            'id_product' => ['type' => 'int', 'length' => 11],
            'id_product_attribute' => ['type' => 'int', 'length' => 11],
            'status' => ['type' => 'varchar', 'length' => 255],
            'date_add' => ['type' => 'datetime', 'length' => 11],
        ],

        'ebay_category' => [
            'id_category_ref' => ['type' => 'int', 'length' => 11],
            'id_ebay_category' => ['type' => 'int', 'length' => 11],
            'id_category_ref_parent' => ['type' => 'int', 'length' => 11],
            'id_country' => ['type' => 'int', 'length' => 11],
            'level' => ['type' => 'tinyint', 'length' => 1],
            'is_multi_sku' => ['type' => 'tinyint', 'length' => 1, 'null' => true],
            'name' => ['type' => 'varchar', 'length' => 255],
            'k_type' => ['type' => 'tinyint', 'length' => 1, 'null' => true],
            'best_offer_enabled' => ['type' => 'tinyint', 'length' => 1, 'null' => true],
        ],

        'ebay_category_condition' => [
            'id_ebay_category_condition' => ['type' => 'int', 'length' => 11],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
            'id_category_ref' => ['type' => 'int', 'length' => 11],
            'id_condition_ref' => ['type' => 'int', 'length' => 11],
            'name' => ['type' => 'varchar', 'length' => 256],
        ],

        'ebay_category_condition_configuration' => [
            'id_ebay_category_condition_configuration' => ['type' => 'int', 'length' => 11],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
            'id_category_ref' => ['type' => 'int', 'length' => 11],
            'condition_type' => ['type' => 'int', 'length' => 11],
            'id_condition_ref' => ['type' => 'int', 'length' => 11],
        ],

        'ebay_category_configuration' => [
            'id_ebay_category_configuration' => ['type' => 'int', 'length' => 11],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
            'id_country' => ['type' => 'int', 'length' => 11],
            'id_ebay_category' => ['type' => 'int', 'length' => 11],
            'id_category' => ['type' => 'int', 'length' => 11],
            'percent' => ['type' => 'varchar', 'length' => 6],
            'sync' => ['type' => 'tinyint', 'length' => 1],
            'date_add' => ['type' => 'datetime', 'length' => null],
            'date_upd' => ['type' => 'datetime', 'length' => null],
        ],

        'ebay_category_specific' => [
            'id_ebay_category_specific' => ['type' => 'int', 'length' => 11],
            'id_category_ref' => ['type' => 'int', 'length' => 11],
            'name' => ['type' => 'varchar', 'length' => 40],
            'required' => ['type' => 'tinyint', 'length' => 1],
            'can_variation' => ['type' => 'tinyint', 'length' => 1],
            'selection_mode' => ['type' => 'tinyint', 'length' => 1],
            'id_attribute_group' => ['type' => 'int', 'length' => 11],
            'id_feature' => ['type' => 'int', 'length' => 11],
            'id_ebay_category_specific_value' => ['type' => 'int', 'length' => 11],
            'is_brand' => ['type' => 'tinyint', 'length' => 1],
            'ebay_site_id' => ['type' => 'int', 'length' => 11],
            'is_reference' => ['type' => 'tinyint', 'length' => 1],
            'is_ean' => ['type' => 'tinyint', 'length' => 1],
            'is_upc' => ['type' => 'tinyint', 'length' => 1],
            'max_values' => ['type' => 'int', 'length' => 2],
        ],

        'ebay_category_specific_value' => [
            'id_ebay_category_specific_value' => ['type' => 'int', 'length' => 11],
            'id_ebay_category_specific' => ['type' => 'int', 'length' => 11],
            'value' => ['type' => 'varchar', 'length' => 50],
        ],

        'ebay_configuration' => [
            'id_configuration' => ['type' => 'int', 'length' => 11],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
            'name' => ['type' => 'varchar', 'length' => 32],
            'value' => ['type' => 'text', 'length' => null],
        ],

        'ebay_delivery_time_options' => [
            'id_delivery_time_option' => ['type' => 'int', 'length' => 11],
            'DispatchTimeMax' => ['type' => 'varchar', 'length' => 256],
            'description' => ['type' => 'varchar', 'length' => 256],
        ],

        'ebay_log' => [
            'id_ebay_log' => ['type' => 'int', 'length' => 11, 'primary' => true],
            'text' => ['type' => 'text', 'length' => 11],
            'type' => ['type' => 'varchar', 'length' => 32],
            'date_add' => ['type' => 'datetime', 'length' => null],
        ],

        'ebay_order' => [
            'id_ebay_order' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_order_ref' => ['type' => 'varchar', 'length' => 128],
            'id_order' => ['type' => 'int', 'length' => 11],
        ],

        'ebay_order_log' => [
            'id_ebay_order_log' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
            'id_ebay_order' => ['type' => 'int', 'length' => 11],
            'id_orders' => ['type' => 'varchar', 'length' => 255],
            'type' => ['type' => 'varchar', 'length' => 40],
            'success' => ['type' => 'tinyint', 'length' => 1],
            'data' => ['type' => 'text', 'length' => null],
            'date_add' => ['type' => 'datetime', 'length' => null],
            'date_update' => ['type' => 'datetime', 'length' => null],
        ],

        'ebay_order_order' => [
            'id_ebay_order_order' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_ebay_order' => ['type' => 'int', 'length' => 11],
            'id_order' => ['type' => 'int', 'length' => 11],
            'id_shop' => ['type' => 'int', 'length' => 11],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
            'id_transaction' => ['type' => 'varchar', 'length' => 125],
        ],

        'ebay_product' => [
            'id_ebay_product' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
            'id_country' => ['type' => 'int', 'length' => 11],
            'id_product' => ['type' => 'int', 'length' => 11],
            'id_attribute' => ['type' => 'int', 'length' => 11],
            'id_product_ref' => ['type' => 'varchar', 'length' => 32],
            'date_add' => ['type' => 'datetime', 'length' => null],
            'date_upd' => ['type' => 'datetime', 'length' => null],
            'id_shipping_policies' => ['type' => 'varchar', 'length' => 125, 'null' => true, 'default' => null],
            'id_category_ps' => ['type' => 'int', 'length' => 11],
        ],

        'ebay_product_configuration' => [
            'id_ebay_product_configuration' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_product' => ['type' => 'int', 'length' => 11],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
            'blacklisted' => ['type' => 'tinyint', 'length' => 1],
            'extra_images' => ['type' => 'int', 'length' => 11],
        ],

        'ebay_product_image' => [
            'id_ebay_product_image' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'ps_image_url' => ['type' => 'varchar', 'length' => 255],
            'ebay_image_url' => ['type' => 'varchar', 'length' => 255],
        ],

        'ebay_product_modified' => [
            'id_ebay_product_modified' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
            'id_product' => ['type' => 'int', 'length' => 11],
        ],

        'ebay_profile' => [
            'id_ebay_profile' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_lang' => ['type' => 'int', 'length' => 11],
            'id_shop' => ['type' => 'int', 'length' => 11],
            'ebay_user_identifier' => ['type' => 'varchar', 'length' => 255],
            'ebay_site_id' => ['type' => 'int', 'length' => 11],
            'id_ebay_returns_policy_configuration' => ['type' => 'int', 'length' => 11],
        ],

        'ebay_returns_policy' => [
            'id_return_policy' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'value' => ['type' => 'varchar', 'length' => 256],
            'description' => ['type' => 'varchar', 'length' => 256],
            'id_country' => ['type' => 'int', 'length' => 11],
        ],

        'ebay_returns_policy_configuration' => [
            'id_ebay_returns_policy_configuration' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'ebay_returns_within' => ['type' => 'varchar', 'length' => 255],
            'ebay_returns_who_pays' => ['type' => 'varchar', 'length' => 255],
            'ebay_returns_description' => ['type' => 'text', 'length' => null],
            'ebay_returns_accepted_option' => ['type' => 'varchar', 'length' => 255],
        ],

        'ebay_returns_policy_description' => [
            'id_return_policy' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'value' => ['type' => 'varchar', 'length' => 256],
            'description' => ['type' => 'varchar', 'length' => 256],
        ],

        'ebay_shipping' => [
            'id_ebay_shipping' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
            'ebay_carrier' => ['type' => 'varchar', 'length' => 256],
            'ps_carrier' => ['type' => 'int', 'length' => 11],
            'extra_fee' => ['type' => 'int', 'length' => 11],
            'international' => ['type' => 'int', 'length' => 11],
            'id_zone' => ['type' => 'int', 'length' => 11],
        ],

        'ebay_shipping_international_zone' => [
            'id_ebay_shipping' => ['type' => 'int', 'length' => 11],
            'id_ebay_zone' => ['type' => 'varchar', 'length' => 256],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
        ],

        'ebay_shipping_location' => [
            'id_ebay_location' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'location' => ['type' => 'varchar', 'length' => 256],
            'description' => ['type' => 'varchar', 'length' => 256],
        ],

        'ebay_shipping_service' => [
            'id_shipping_service' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'description' => ['type' => 'varchar', 'length' => 256],
            'shippingService' => ['type' => 'varchar', 'length' => 256],
            'shippingServiceID' => ['type' => 'varchar', 'length' => 256],
            'InternationalService' => ['type' => 'varchar', 'length' => 256],
            'ServiceType' => ['type' => 'varchar', 'length' => 256],
            'ebay_site_id' => ['type' => 'int', 'length' => 11],
        ],

        'ebay_shipping_zone_excluded' => [
            'id_ebay_zone_excluded' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
            'region' => ['type' => 'varchar', 'length' => 255],
            'location' => ['type' => 'varchar', 'length' => 255],
            'description' => ['type' => 'varchar', 'length' => 255],
            'excluded' => ['type' => 'int', 'length' => 11],
        ],

        'ebay_stat' => [
            'id_ebay_stat' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
            'version' => ['type' => 'varchar', 'length' => 10],
            'data' => ['type' => 'text', 'length' => null, 'null' => false],
            'date_add' => ['type' => 'datetime', 'length' => null],
            'tries' => ['type' => 'tinyint', 'length' => 3, 'unsigned' => true],
        ],

        'ebay_store_category' => [
            'id_ebay_store_category' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
            'ebay_category_id' => ['type' => 'varchar', 'length' => 255],
            'name' => ['type' => 'varchar', 'length' => 255],
            'order' => ['type' => 'int', 'length' => 11],
            'ebay_parent_category_id' => ['type' => 'varchar', 'length' => 255],
        ],

        'ebay_store_category_configuration' => [
            'id_ebay_store_category_configuration' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_ebay_profile' => ['type' => 'int', 'length' => 11],
            'ebay_category_id' => ['type' => 'varchar', 'length' => 255],
            'id_category' => ['type' => 'int', 'length' => 11],
        ],

        'ebay_sync_history' => [
            'id_ebay_sync_history' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'is_manual' => ['type' => 'tinyint', 'length' => 1],
            'datetime' => ['type' => 'datetime', 'length' => null],
        ],

        'ebay_sync_history_product' => [
            'id_ebay_sync_history_product' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_ebay_sync_history' => ['type' => 'int', 'length' => 11],
            'id_product' => ['type' => 'int', 'length' => 11],
        ],

        'ebay_user_identifier_token' => [
            'ebay_user_identifier' => ['type' => 'varchar', 'length' => 255, 'primary' => true],
            'token' => ['type' => 'text', 'length' => null],
        ],
        'ebay_business_policies' => [
            'id' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'type' => ['type' => 'varchar', 'length' => 255],
            'name' => ['type' => 'varchar', 'length' => 255],
            'id_bussines_Policie' => ['type' => 'varchar', 'length' => 30],
            'id_ebay_profile' => ['type' => 'int', 'length' => 16],
        ],
        'ebay_category_business_config' => [
            'id' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_category' => ['type' => 'int', 'length' => 11],
            'id_return' => ['type' => 'varchar', 'length' => 30],
            'id_payment' => ['type' => 'varchar', 'length' => 30],
            'id_ebay_profile' => ['type' => 'int', 'length' => 16],
        ],
        'ebay_order_return_detail' => [
            'id' => ['type' => 'int', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_return' => ['type' => 'varchar', 'length' => 125],
            'type' => ['type' => 'varchar', 'length' => 125],
            'date' => ['type' => 'datetime', 'length' => null],
            'description' => ['type' => 'varchar', 'length' => 125],
            'status' => ['type' => 'varchar', 'length' => 125],
            'id_order' => ['type' => 'varchar', 'length' => 125],
            'id_ebay_order' => ['type' => 'varchar', 'length' => 125],
            'id_transaction' => ['type' => 'varchar', 'length' => 125],
            'id_item' => ['type' => 'varchar', 'length' => 125],
        ],
        'ebay_order_errors' => [
            'id_ebay_order_error' => ['type' => 'int', 'length' => 30, 'primary' => true, 'auto_increment' => true],
            'error' => ['type' => 'varchar', 'length' => 255],
            'id_order_seller' => ['type' => 'int', 'length' => 30],
            'id_order_ebay' => ['type' => 'varchar', 'length' => 255],
            'ebay_user_identifier' => ['type' => ' varchar', 'length' => 255],
            'total' => ['type' => 'int', 'length' => 30],
            'email' => ['type' => 'varchar', 'length' => 255],
            'date_order' => ['type' => 'datetime', 'length' => null],
            'date_add' => ['type' => 'TIMESTAMP', 'length' => null],
            'date_upd' => ['type' => 'TIMESTAMP', 'length' => null],
        ],
        'ebay_task_manager' => [
            'id' => ['type' => 'INT', 'length' => 30, 'primary' => true, 'auto_increment' => true],
            'id_product' => ['type' => 'INT', 'length' => 30],
            'id_product_attribute' => ['type' => ' INT', 'length' => 30],
            'id_task' => ['type' => ' INT', 'length' => 30],
            'id_ebay_profile' => ['type' => ' INT', 'length' => 30],
            'error' => ['type' => ' text', 'length' => null, 'null' => true],
            'error_code' => ['type' => 'int', 'length' => 30, 'null' => true],
            'date_add' => ['type' => 'TIMESTAMP', 'length' => null],
            'date_upd' => ['type' => 'TIMESTAMP', 'length' => null],
            'retry' => ['type' => 'int', 'length' => 30],
            'locked' => ['type' => ' VARCHAR', 'length' => 125, 'null' => true],
            'priority' => ['type' => 'int', 'length' => 2, 'null' => true],
        ],
        'ebay_catalog_configuration' => [
            'id' => ['type' => 'INT', 'length' => 11, 'primary' => true, 'auto_increment' => true],
            'id_country' => ['type' => 'INT', 'length' => 11],
            'name' => ['type' => 'VARCHAR', 'length' => 250],
            'value' => ['type' => 'VARCHAR', 'length' => 250],
        ],
    ];

    public function checkDatabase($withOutLogs = true)
    {
        foreach ($this->database as $table => $fields) {
            $this->checkTable($table, $fields);
        }
        if ($withOutLogs) {
            $this->writeLog();
        }
    }

    public function checkTemplate()
    {
        $id_shop = Shop::getContextShopID();
        $profiles = EbayProfile::getProfilesByIdShop($id_shop);
        foreach ($profiles as $profile_ebay) {
            $profile = new EbayProfile($profile_ebay['id_ebay_profile']);

            $template = $profile->getConfiguration('EBAY_PRODUCT_TEMPLATE');
            if (preg_match('/headerSearchProductPrestashop/i', $template)) {
                $template = preg_replace("/\s*\<form\>[^)]*\<\/form\>/", '', $template);
                $profile->setConfiguration('EBAY_PRODUCT_TEMPLATE', $template, true);
            }
        }
    }

    public function writeLog()
    {
        $handle = fopen(dirname(__FILE__) . '/../log/log_database.txt', 'a+');
        fwrite($handle, print_r($this->logs, true));
        fclose($handle);
    }

    private function checkTable($table, $fields)
    {
        // Check if table exist
        $result = Db::getInstance()->ExecuteS('SHOW TABLES LIKE "' . _DB_PREFIX_ . $table . '"');

        if ($result === false) {
            $this->setLog($table, 'error', 'SQL REQUEST : SHOW TABLES LIKE "' . _DB_PREFIX_ . $table . '"', Db::getInstance()->getMsgError());
        } elseif (empty($result)) {
            $this->repairTable($table);
        } else {
            $this->checkField($table, $fields);
        }
    }

    private function checkField($table, $fields)
    {
        foreach ($fields as $field => $arguments) {
            $result = Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . $table . '` LIKE \'' . $field . '\';');

            if ($result === false) {
                $this->setLog($table, 'error', 'SQL REQUEST : ' . Db::getInstance()->getMsgError());
            } elseif (empty($result)) {
                $this->setLog($table, 'error', 'The ' . $field . ' column in ' . $table . ' table doesn\'t exist');
                if ($this->addColumns($table, $field, $arguments)) {
                    $this->setLog($table, 'SUCCESSFULL', 'The ' . $field . ' column in ' . $table . ' added', 'SUCCESSFULL');
                }
            } else {
                $this->checkTypeFields($table, $field, $arguments);
            }
        }
    }

    private function addColumns($table, $field, $arguments)
    {
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . $table . '` 
                ADD ' . bqSQL($field) . ' ' . bqSQL($arguments['type']);
        $sql .= ($arguments['length'] ? ('(' . pSQL($arguments['length']) . ')') : '');

        return Db::getInstance()->Execute($sql);
    }

    private function checkTypeFields($table, $field, $arguments)
    {
        $sql = 'SHOW FIELDS FROM `' . _DB_PREFIX_ . $table . '` LIKE "' . $field . '";';
        $result = Db::getInstance()->ExecuteS($sql);

        $reset = false;
        if ($result && !empty($result[0])) {
            // Check Null Attribute
            if (isset($result[0]['Null'])
                &&
                (
                    ($result[0]['Null'] == 'YES' && !isset($arguments['null']))
                    ||
                    ($result[0]['Null'] == 'NO' && isset($arguments['null']))
                )
            ) {
                $reset = true;
            }

            // Check Type Attribute
            if (isset($result[0]['Type']) && isset($arguments['type'])) {
                // is int
                if (preg_match('/^int\(([0-9]+)\)$/', $result[0]['Type'], $rs)) {
                    if (($arguments['type'] != 'int')
                        ||
                        (!isset($arguments['length']) || $rs[1] < $arguments['length'])
                    ) {
                        $reset = true;
                    }
                }

                // is tinyint
                if (preg_match('/^tinyint\(([0-9]+)\)$/', $result[0]['Type'], $rs)) {
                    if (($arguments['type'] != 'tinyint')
                        ||
                        (!isset($arguments['length']) || $rs[1] < $arguments['length'])
                    ) {
                        $reset = true;
                    }
                }

                // is varchar
                if (preg_match('/^varchar\(([0-9]+)\)$/', $result[0]['Type'], $rs)) {
                    if (($arguments['type'] != 'varchar')
                        ||
                        (!isset($arguments['length']) || $rs[1] < $arguments['length'])
                    ) {
                        $reset = true;
                    }
                }

                // is text
                if (preg_match('/^text$/', $result[0]['Type'])
                    && ($arguments['type'] != 'text')) {
                    $reset = true;
                }

                // is datetime
                if (preg_match('/^datetime$/', $result[0]['Type'])
                    && ($arguments['type'] != 'datetime')) {
                    $reset = true;
                }
            }

            // Check default
            if (isset($result[0]['Default'])) {
                if (!isset($arguments['default'])
                    ||
                    ($result[0]['Default'] !== $arguments['default'])
                ) {
                    $reset = true;
                }
            }

            // Check Primary Attribute
            if (isset($result[0]['Key']) && $result[0]['Key'] == 'PRI') {
                // is primary
                if (isset($arguments['primary']) && $arguments['primary']) {
                    // Alter Table Primary
                } else {
                    // Delete primary
                }
            }

            // Check Extra Attribute
            if (isset($result[0]['Extra']) && $result[0]['Extra'] == 'auto_increment') {
                // is primary
                if (isset($arguments['auto_increment']) && $arguments['auto_increment']) {
                    // Alter Table Primary
                } else {
                    // Delete primary
                }
            }
            if ($result[0]['Extra'] != 'auto_increment' && isset($arguments['auto_increment'])) {
                $reset = true;
            }

            if ($reset) {
                $this->repairField($table, $field, $arguments);
            } else {
                $this->setLog($table, 'success', 'The ' . $field . ' field in ' . $table . ' is good');
            }
        } else {
            $this->setLog($table, 'error', 'SQL REQUEST : ' . $sql);
        }
    }

    private function repairTable($table)
    {
        $this->setLog($table, 'error', 'The ' . $table . ' table doesn\'t exist');

        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . $table . '` ( ';
        $primary_key = []; //Case of double primary key

        foreach ($this->database[$table] as $column => $arguments) {
            $sql .= '`' . $column . '` ' . $arguments['type'];

            if ($arguments['length']) {
                $sql .= '(' . $arguments['length'] . ')';
            }

            if (isset($arguments['unsigned']) && $arguments['unsigned']) {
                $sql .= ' unsigned ';
            }

            if (isset($arguments['null'])) {
                if ($arguments['null']) {
                    $sql .= ' NULL ';
                }
            } else {
                $sql .= ' NOT NULL ';
            }

            if (isset($arguments['auto_increment']) && $arguments['auto_increment']) {
                $sql .= ' AUTO_INCREMENT ';
            }

            if (isset($arguments['primary']) && $arguments['primary']) {
                $primary_key[] = $column;
            }

            $sql .= ', ';
        }

        foreach ($primary_key as $column) {
            $sql .= 'PRIMARY KEY (`' . $column . '`)';
        }

        $sql .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8';

        $result = Db::getInstance()->Execute($sql);

        if ($result) {
            $this->setLog($table, 'error', 'SQL REQUEST : ' . $sql, 'SUCCESSFULL');
        } else {
            $this->setLog($table, 'error', 'SQL REQUEST : ' . $sql, Db::getInstance()->getMsgError());
        }
    }

    private function repairField($table, $field, $arguments)
    {
        // ALTER TABLE `ps_ebay_api_log` CHANGE `id_ebay_profile` `id_ebay_profile` INT(16) NOT NULL;
        // ALTER TABLE `ps_ebay_api_log` CHANGE `id_ebay_api_log` `id_ebay_api_log` INT(16) NOT NULL AUTO_INCREMENT;
        // ALTER TABLE `ps_ebay_configuration` CHANGE `id_ebay_profile` `id_ebay_profile` INT(11) UNSIGNED NULL DEFAULT NULL;
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . $table . '` CHANGE `' . $field . '` `' . $field . '` ' . $arguments['type'] . '';

        if (isset($arguments['length']) && $arguments['length']) {
            $sql .= '(' . $arguments['length'] . ') ';
        } else {
            $sql .= ' ';
        }

        if (isset($arguments['unsigned']) && $arguments['unsigned']) {
            $sql .= 'UNSIGNED ';
        }

        if (!isset($arguments['null'])) {
            $sql .= 'NOT NULL ';
        } elseif ($arguments['null']) {
            $sql .= 'NULL ';
        }

        if (array_key_exists('default', $arguments)) {
            if ($arguments['default'] === null) {
                $sql .= 'DEFAULT NULL ';
            }
        }

        if (isset($arguments['auto_increment']) && $arguments['auto_increment']) {
            $sql .= 'AUTO_INCREMENT ';
        }

        $result = Db::getInstance()->Execute($sql);

        if ($result) {
            $this->setLog($table, 'execute-success', 'SQL REQUEST : ' . $sql, 'SUCCESSFULL');
        } else {
            $this->setLog($table, 'error', 'SQL REQUEST : ' . $sql, Db::getInstance()->getMsgError());
        }
    }

    private function setLog($table, $status, $action, $result = '')
    {
        $this->logs[$table][] = ['status' => $status, 'action' => $action, 'result' => $result];
    }

    public function getNbTable()
    {
        return count($this->database);
    }

    public function checkSpecificTable($id)
    {
        $array = array_keys($this->database);

        if (array_key_exists($id - 1, $array)) {
            $this->checkTable($array[$id - 1], $this->database[$array[$id - 1]]);

            return true;
        }

        return false;
    }

    public function getLogForSpecificTable($id)
    {
        $array = array_keys($this->database);

        if (array_key_exists($id - 1, $array)) {
            return $this->logs[$array[$id - 1]];
        }

        return false;
    }

    public function getLog()
    {
        $result = [];

        foreach ($this->logs as $table => $logs) {
            foreach ($logs as $log) {
                if ($log['status'] != 'success') {
                    $result[$table][] = ['status' => $log['status'], 'action' => $log['action'], 'result' => $log['result']];
                }
            }

            if (!array_key_exists($table, $result)) {
                $result[$table][] = ['status' => 'success', 'action' => 'No action on ' . $table . ' table', 'result' => 'The table is perfect'];
            }
        }

        return $result;
    }

    public function getCategoriesPsEbay()
    {
        $sql = 'SELECT
			DISTINCT(ec1.`id_categories`) as id,
			CONCAT(
				IFNULL(ec3.`name`, \'\'),
				IF (ec3.`name` is not null, \' > \', \'\'),
				IFNULL(ec2.`name`, \'\'),
				IF (ec2.`name` is not null, \' > \', \'\'),
				ec1.`name`
			) as name
			FROM `' . _DB_PREFIX_ . 'ebay_category_tmp` ec1
			LEFT JOIN `' . _DB_PREFIX_ . 'ebay_category_tmp` ec2
			ON ec1.`id_categories_ref_parent` = ec2.`id_categories`
			AND ec1.`id_categories_ref_parent` <> \'1\'
			AND ec1.level <> 1
			LEFT JOIN `' . _DB_PREFIX_ . 'ebay_category_tmp` ec3
			ON ec2.`id_categories_ref_parent` = ec3.`id_categories`
			AND ec2.`id_categories_ref_parent` <> \'1\'
			AND ec2.level <> 1
			WHERE ec1.`id_categories` is not null and ec1.`id_categories` not in(
			SELECT `id_category_ref`
			FROM `' . _DB_PREFIX_ . 'ebay_category` ecp
			WHERE ecp. `id_category_ref` is not null)';

        return Db::getInstance()->executeS($sql);
    }

    public function getCategoriesTmp()
    {
        $sql = 'SELECT
			DISTINCT(ec1.`id_categories`) as id,
			CONCAT(
				IFNULL(ec3.`name`, \'\'),
				IF (ec3.`name` is not null, \' > \', \'\'),
				IFNULL(ec2.`name`, \'\'),
				IF (ec2.`name` is not null, \' > \', \'\'),
				ec1.`name`
			) as name
			FROM `' . _DB_PREFIX_ . 'ebay_category_tmp` ec1
			LEFT JOIN `' . _DB_PREFIX_ . 'ebay_category_tmp` ec2
			ON ec1.`id_categories_ref_parent` = ec2.`id_categories`
			AND ec1.`id_categories_ref_parent` <> \'1\'
			AND ec1.level <> 1
			LEFT JOIN `' . _DB_PREFIX_ . 'ebay_category_tmp` ec3
			ON ec2.`id_categories_ref_parent` = ec3.`id_categories`
			AND ec2.`id_categories_ref_parent` <> \'1\'
			AND ec2.level <> 1
			WHERE ec1.`id_categories` is not null
			AND ec1.id_categories in (SELECT ec.id_category_ref FROM ' . _DB_PREFIX_ . 'ebay_category_configuration ecc
			    LEFT JOIN ' . _DB_PREFIX_ . 'ebay_category ec 
			    ON ecc.id_ebay_category = ec.id_ebay_category)';

        return Db::getInstance()->executeS($sql);
    }

    public function comparationCategories($ebay_profile_id)
    {
        $data = [];
        $result = [];
        $data['cat_ebay'] = $this->getCategoriesTmp();
        $data['cat_ps'] = EbayCategoryConfiguration::getEbayCategories($ebay_profile_id);

        if ($data['cat_ps'] == '') {
            $result['table'] = null;
            $result['new'] = false;
            $datas = [];

            return $datas[] = $result;
        }
        $result = [];
        foreach ($data['cat_ps'] as $cat_p) {
            foreach ($data['cat_ebay'] as $cat_ebay) {
                $statut = 0;
                if ($cat_p['id'] == $cat_ebay['id'] and $cat_p['name'] == $cat_ebay['name']) {
                    $result['table'][$cat_p['name']] = 1;

                    $statut = 1;
                    break;
                }
            }
            if ($statut == 0) {
                $result['table'][$cat_p['name']] = 0;
            }
        }

        $ps_ebay_cat = $this->getCategoriesPsEbay();
        if ($ps_ebay_cat == '') {
            $result['new'] = false;
        }
        $result['new'] = $ps_ebay_cat;
        $datas = [];

        return $datas[] = $result;
    }

    public function deleteTmp()
    {
        Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ebay_category_tmp`');
    }
}
