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
 *  @copyright 2007-2020 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

require_once dirname(__FILE__).'/EbayRequest.php';

class EbayProductImage
{
    public static function getEbayUrl($ps_url, $ebay_image_name)
    {
        $db = Db::getInstance();

        $ebay_url = $db->getValue('SELECT `ebay_image_url` from `'._DB_PREFIX_.'ebay_product_image`
			WHERE `ps_image_url` = \''.pSQL($ps_url).'\'');

        if (!$ebay_url) {
            $ebay_request = new EbayRequest();
            $ebay_url = $ebay_request->uploadSiteHostedPicture($ps_url, $ebay_image_name);

            if (!$ebay_url) {
                return $ps_url;
            }

            $data = array(
                'ps_image_url' => pSQL($ps_url),
                'ebay_image_url' => pSQL($ebay_url),
            );

            $db->insert('ebay_product_image', $data);
        }

        return $ebay_url;
    }

    public static function removeAllProductImage()
    {

        return Db::getInstance()->Execute('TRUNCATE '._DB_PREFIX_.'ebay_product_image');
    }
}
