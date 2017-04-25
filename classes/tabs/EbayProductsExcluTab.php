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

class EbayProductsExcluTab extends EbayTab
{

    public function getContent($ebay_profile)
    {

        $ids_products = EbayProduct::getProductsBlocked($ebay_profile->id);
        $products = array();
        if(!empty($ids_products)) {
            foreach ($ids_products as $product_id) {
                $product = new Product($product_id['id_product'], false, $ebay_profile->id_lang);
                $products[] = array(
                    'id_product' => $product_id['id_product'],
                    'name' => $product->name
                );
            }
        }
        $vars = array(
            'products' => $products,
            'id_ebay_profile' => $ebay_profile->id,
            'ebay_token' => Configuration::get('EBAY_SECURITY_TOKEN'),
        );

        return $this->display('tableProductsExclu.tpl', $vars);
    }
}
