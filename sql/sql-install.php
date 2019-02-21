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
 *  @copyright 2007-2019 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

// Init
$sql = array();

// Create Category Table in Database
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_category` (
		  `id_ebay_category` int(16) NOT NULL AUTO_INCREMENT,
		  `id_category_ref` int(16) NOT NULL,
		  `id_category_ref_parent` int(16) NOT NULL,
		  `id_country` int(16) NOT NULL,
		  `level` tinyint(1) NOT NULL,
		  `is_multi_sku` tinyint(1),
		  `name` varchar(255) NOT NULL,
		  `k_type` tinyint(1),
		  `best_offer_enabled` tinyint(1),
		  UNIQUE(`id_category_ref`, `id_country`),
		  PRIMARY KEY  (`id_ebay_category`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

// Create Configuration Table in Database
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_category_configuration` (
		  `id_ebay_category_configuration` int(16) NOT NULL AUTO_INCREMENT,
		  `id_ebay_profile` INT( 16 ) NOT NULL,
		  `id_country` int(16) NOT NULL,
		  `id_ebay_category` int(16) NOT NULL,
		  `id_category` int(16) NOT NULL,
		  `percent` varchar(4) NOT NULL,
		  `sync` tinyint(1) NOT NULL,
		  `date_add` datetime NOT NULL,
		  `date_upd` datetime NOT NULL,
		  PRIMARY KEY  (`id_ebay_category_configuration`),
		  KEY `id_ebay_category` (`id_ebay_profile`, `id_ebay_category`),
		  KEY `id_category` (`id_ebay_profile`, `id_category`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

// Create Category Table in Database
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_product` (
		  `id_ebay_product` int(16) NOT NULL AUTO_INCREMENT,
			`id_ebay_profile` int(11) unsigned DEFAULT NULL,
		  `id_country` int(16) NOT NULL,
	 	  `id_product` int(16) NOT NULL,
	 	  `id_attribute` int(16) NOT NULL,
		  `id_product_ref` varchar(32) NOT NULL,
		  `date_add` datetime NOT NULL,
		  `date_upd` datetime NOT NULL,
		  `id_shipping_policies` varchar(255) DEFAULT NULL,
		  `id_category_ps` int(16) NOT NULL,
		  UNIQUE(`id_product_ref`),
		  PRIMARY KEY  (`id_ebay_product`),
		  KEY `id_product` (`id_product`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

// Create Order Table in Database
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_order` (
		  `id_ebay_order` int(16) NOT NULL AUTO_INCREMENT,
		  `id_order_ref` varchar(128) NOT NULL,
		  `id_order` int(16) NOT NULL,
		  UNIQUE(`id_order_ref`),
		  PRIMARY KEY  (`id_ebay_order`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

// Create Sync History Table in Database
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_sync_history` (
		  `id_ebay_sync_history` int(16) NOT NULL AUTO_INCREMENT,
		  `is_manual` tinyint(1) NOT NULL,
		  `datetime` datetime NOT NULL,
		  PRIMARY KEY  (`id_ebay_sync_history`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

// Create Sync History Product Table in Database
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_sync_history_product` (
		  `id_ebay_sync_history_product` int(16) NOT NULL AUTO_INCREMENT,
		  `id_ebay_sync_history` int(16),
		  `id_product` int(16),
		  KEY (`id_ebay_sync_history`),
		  PRIMARY KEY  (`id_ebay_sync_history_product`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

// SHIPPING CARRIER
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_shipping` (
		  `id_ebay_shipping` int(11) NOT NULL AUTO_INCREMENT,
			`id_ebay_profile` int(16) NOT NULL,
		  `ebay_carrier` varchar(256) NOT NULL,
		  `ps_carrier` int(11) NOT NULL,
		  `extra_fee` float(8,2) NOT NULL,
		  `international` int(4) NOT NULL,
		  `id_zone`int(11) NOT NULL,
		  PRIMARY KEY (`id_ebay_shipping`)
	) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_shipping_zone_excluded` (
			  `id_ebay_zone_excluded` int(11) NOT NULL AUTO_INCREMENT,
			  `id_ebay_profile` int(16) NOT NULL,
			  `region` varchar(255) NOT NULL,
			  `location` varchar(255) NOT NULL,
			  `description` varchar(255) NOT NULL,
			  `excluded` int(2) NOT NULL,
			  PRIMARY KEY (`id_ebay_zone_excluded`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_shipping_international_zone` (
		  `id_ebay_shipping` int(11) NOT NULL,
		  `id_ebay_zone` varchar(256) NOT NULL,
		  `id_ebay_profile` int(16) NOT NULL
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_shipping_location` (
		  `id_ebay_location` int(11) NOT NULL AUTO_INCREMENT,
		  `location` varchar(256) NOT NULL,
		  `description` varchar(256) NOT NULL,
		  PRIMARY KEY (`id_ebay_location`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_delivery_time_options` (
		  `id_delivery_time_option` int(11) NOT NULL AUTO_INCREMENT,
		  `DispatchTimeMax` varchar(256) NOT NULL,
		  `description` varchar(256) NOT NULL,
		  PRIMARY KEY (`id_delivery_time_option`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_shipping_service` (
		  `id_shipping_service` int(11) NOT NULL AUTO_INCREMENT,
		  `description` varchar(256) NOT NULL,
		  `shippingService` varchar(256) NOT NULL,
		  `shippingServiceID` varchar(256) NOT NULL,
		  `InternationalService` varchar(256) NOT NULL,
		  `ServiceType` varchar(256) NOT NULL,
          `ebay_site_id` int(16) NOT NULL,
		  PRIMARY KEY (`id_shipping_service`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_returns_policy` (
		  `id_return_policy` int(11) NOT NULL AUTO_INCREMENT,
		  `value` varchar(256) NOT NULL,
		  `description` varchar(256) NOT NULL,
		  `id_country` INT(11),
		  PRIMARY KEY (`id_return_policy`)
		) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_returns_policy_description` (
		  `id_return_policy` int(11) NOT NULL AUTO_INCREMENT,
		  `value` varchar(256) NOT NULL,
		  `description` varchar(256) NOT NULL,
		  PRIMARY KEY (`id_return_policy`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_category_specific` (
		  `id_ebay_category_specific` int(11) NOT NULL AUTO_INCREMENT,
		  `id_category_ref` int(16) NOT NULL,
		  `name` varchar(40) NOT NULL,
			`required` tinyint(1) NOT NULL,
			`can_variation` tinyint(1) NOT NULL,
			`selection_mode` tinyint(1) NOT NULL,
		  `id_attribute_group` int(16) NULL,
		  `id_feature` int(16) NULL,
		  `id_ebay_category_specific_value` int(16) NULL,
		  `is_brand` tinyint(1) NULL,
          `ebay_site_id` int(16) NOT NULL,
          `is_reference` tinyint(1) NULL,
          `is_ean` tinyint(1) NULL,
          `is_upc` tinyint(1) NULL,
          `max_values` int(2),
		  UNIQUE(`id_category_ref`, `ebay_site_id`, `name`),
		  PRIMARY KEY (`id_ebay_category_specific`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_category_specific_value` (
			`id_ebay_category_specific_value` int(11) NOT NULL AUTO_INCREMENT,
		  `id_ebay_category_specific` int(11) NOT NULL,
		  `value` varchar(50) NOT NULL,
		  UNIQUE(`id_ebay_category_specific`, `value`),
		  PRIMARY KEY (`id_ebay_category_specific_value`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_category_condition` (
			`id_ebay_category_condition` int(11) NOT NULL AUTO_INCREMENT,
			`id_ebay_profile` int(11) unsigned DEFAULT NULL,
			`id_category_ref` int(11) NOT NULL,
			`id_condition_ref` int(11) NOT NULL,
		  `name` varchar(256) NOT NULL,
		  PRIMARY KEY (`id_ebay_category_condition`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_category_condition_configuration` (
			`id_ebay_category_condition_configuration` int(11) NOT NULL AUTO_INCREMENT,
			`id_ebay_profile` int(11) unsigned DEFAULT NULL,
			`id_category_ref` int(11) NOT NULL,
			`condition_type` int(11) NOT NULL,
			`id_condition_ref` int(11) NOT NULL,
		  UNIQUE(`id_category_ref`, `condition_type`),
		  PRIMARY KEY (`id_ebay_category_condition_configuration`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_product_configuration` (
			  `id_ebay_product_configuration` int(11) NOT NULL AUTO_INCREMENT,
				`id_product` int(16),
                `id_ebay_profile` INT( 16 ) NOT NULL,
				`blacklisted` tinyint(1) NOT NULL,
				`extra_images` int(4) NOT NULL,
				UNIQUE(`id_product`, `id_ebay_profile`),
			  PRIMARY KEY (`id_ebay_product_configuration`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_product_image` (
			  `id_ebay_product_image` int(11) NOT NULL AUTO_INCREMENT,
				`ps_image_url` varchar(255),
				`ebay_image_url` varchar(255),
				UNIQUE(`ps_image_url`),
			  PRIMARY KEY (`id_ebay_product_image`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_profile` (
		  `id_ebay_profile` int(16) NOT NULL AUTO_INCREMENT,
		  `id_lang` int(10) NOT NULL,
		  `id_shop` int(11) NOT NULL,
		  `ebay_user_identifier` varchar(255) NOT NULL,
			`ebay_site_id` int(10) NOT NULL,
			`id_ebay_returns_policy_configuration` int(10) unsigned NOT NULL,
		  PRIMARY KEY  (`id_ebay_profile`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_configuration` (
	  `id_configuration` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `id_ebay_profile` int(11) unsigned DEFAULT NULL,
	  `name` varchar(32) NOT NULL,
	  `value` text,
	  PRIMARY KEY (`id_configuration`),
		UNIQUE(`id_ebay_profile`, `name`),
	  KEY `name` (`name`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_returns_policy_configuration` (
	  `id_ebay_returns_policy_configuration` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`ebay_returns_within` varchar(255) NOT NULL,
		`ebay_returns_who_pays` varchar(255) NOT NULL,
		`ebay_returns_description` text NOT NULL,
		`ebay_returns_accepted_option` varchar(255) NOT NULL,
	  PRIMARY KEY (`id_ebay_returns_policy_configuration`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_order_order` (
	`id_ebay_order_order` int(16) unsigned NOT NULL AUTO_INCREMENT,
	`id_ebay_order` int(16) NOT NULL,
	`id_order` int(16) NOT NULL,
	`id_shop` int(16) NOT NULL,
	`id_ebay_profile` INT NULL,
	`id_transaction` VARCHAR(125),
	PRIMARY KEY  (`id_ebay_order_order`),
    UNIQUE KEY  (`id_order`, `id_shop`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_product_modified` (
	`id_ebay_product_modified` int(16) unsigned NOT NULL AUTO_INCREMENT,
	`id_ebay_profile` int(16) NOT NULL,
    `id_product` int(16) NOT NULL,
	PRIMARY KEY  (`id_ebay_product_modified`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_log` (
	`id_ebay_log` int(16) unsigned NOT NULL AUTO_INCREMENT,
	`text` text NOT NULL,
	`type` varchar(40) NOT NULL,
    `date_add` datetime NOT NULL,
	PRIMARY KEY  (`id_ebay_log`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_logs` (
    `id_ebay_logs` INT(16) NOT NULL AUTO_INCREMENT,
    `datetime` DATETIME NOT NULL,
    `severity` TINYINT(1) NOT NULL DEFAULT 0,
    `code` INT(11) NOT NULL DEFAULT 0,
    `message` TEXT,
    `context` TEXT,
    `backtrace` TEXT,
    `uid` TEXT,
    PRIMARY KEY (`id_ebay_logs`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_stat` (
	`id_ebay_stat` int(16) NOT NULL AUTO_INCREMENT,
	`id_ebay_profile` int(16) NOT NULL,
	`version` varchar(10) NOT NULL,
    `data` text,
    `date_add` datetime NOT NULL,
    `tries` TINYINT unsigned NOT NULL,
	PRIMARY KEY  (`id_ebay_stat`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_user_identifier_token` (
    `ebay_user_identifier` varchar(255) NOT NULL,
    `token` text NOT NULL,
    PRIMARY KEY (`ebay_user_identifier`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_store_category` (
    `id_ebay_store_category` int(16) NOT NULL AUTO_INCREMENT,
	`id_ebay_profile` int(16) NOT NULL,
	`ebay_category_id` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	`order` int(16) NOT NULL,
	`ebay_parent_category_id` varchar(255) NOT NULL,
	UNIQUE(`id_ebay_profile`, `ebay_category_id`),
	PRIMARY KEY  (`id_ebay_store_category`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_store_category_configuration` (
    `id_ebay_store_category_configuration` int(16) NOT NULL AUTO_INCREMENT,
	`id_ebay_profile` int(16) NOT NULL,
    `ebay_category_id` varchar(255) NOT NULL,
    `id_category` int(16) NOT NULL,
	UNIQUE(`id_ebay_profile`, `id_category`),
	PRIMARY KEY  (`id_ebay_store_category_configuration`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_api_log` (
	`id_ebay_api_log` int(16) NOT NULL AUTO_INCREMENT,
	`id_ebay_profile` int(16) NOT NULL,
	`type` varchar(40) NOT NULL,
	`context` varchar(40) NOT NULL,
	`data_sent` text NOT NULL,
	`response` text NOT NULL,
	`id_product` int(16),
	`id_order` int(16),
    `date_add` datetime NOT NULL,
    `request` text,
    `status` varchar(255),
	PRIMARY KEY  (`id_ebay_api_log`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_order_log` (
	`id_ebay_order_log` int(16) NOT NULL AUTO_INCREMENT,
	`id_ebay_profile` int(16) NOT NULL,
    `id_ebay_order` int(16) NOT NULL,
    `id_orders` varchar(255),
    `type` varchar(40) NOT NULL,
	`success` tinyint(1) NOT NULL,
	`data` text,
    `date_add` datetime NOT NULL,
    `date_update` datetime ,
	PRIMARY KEY  (`id_ebay_order_log`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_business_policies` (
    `id` INT(6)AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(30) NOT NULL,
    `name` VARCHAR(225) NOT NULL,
    `id_bussines_Policie` VARCHAR(30),
    `id_ebay_profile` int(16)
)ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_category_business_config` (
    `id` INT(30)AUTO_INCREMENT PRIMARY KEY,
    `id_category` int(11) NOT NULL,
    `id_return` VARCHAR(30) ,
    `id_payment` VARCHAR(30),
    `id_ebay_profile` int(16)
)ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_order_return_detail` (
    `id` INT(30)AUTO_INCREMENT PRIMARY KEY,
    `id_return` VARCHAR(125)NOT NULL,
    `type` VARCHAR(125),
    `date` datetime ,
    `description` VARCHAR(255),
    `status` VARCHAR(125),
    `id_order` VARCHAR(125),
    `id_ebay_order` VARCHAR(125),
    `id_transaction` VARCHAR(125),
    `id_item` VARCHAR(125)
)ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_task_manager` (
    `id` INT(30)AUTO_INCREMENT PRIMARY KEY,
    `id_product` INT(30),
    `id_product_attribute` INT(30),
    `id_task` INT(30),
    `id_ebay_profile` INT(30),
    `error` text,
    `error_code` INT(30) NULL DEFAULT NULL,
    `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `date_upd` datetime,
    `retry` INT(30),
    `locked` VARCHAR(125),
    `priority` INT(2) NULL DEFAULT NULL
)ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'ebay_catalog_configuration (
            `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
            `id_country` INT(11),
            `name` VARCHAR(250),
            `value` VARCHAR(250)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_best_offers` (
	`id_best_offer` int(16) NOT NULL AUTO_INCREMENT,
	`id_ebay_profile` int(16) NOT NULL,
	`itemid` varchar(40) NOT NULL,
	`best_offer_ebay_id` varchar(40) NOT NULL,
	`id_product` int(16) NOT NULL,
	`seller_message` text,
	`id_product_attribute` int(16),
	`status` VARCHAR(125),
    `date_add` datetime NOT NULL,
    `expirationTime` datetime,
    `price` float(8,2),
    `quantity` int(16),
    `product_title` VARCHAR(125),
	PRIMARY KEY  (`id_best_offer`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

