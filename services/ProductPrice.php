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
 *
 */

class ProductPrice
{
    protected $ebayProfile;

    /** @var ProductTax*/
    protected $productTax;

    public function __construct(EbayProfile $ebayProfile)
    {
        $this->ebayProfile = $ebayProfile;
        $this->productTax = new ProductTax($ebayProfile);
    }

    public function getPriceById($idProduct, $params = [])
    {
        $product = new Product($idProduct);
        $idCountry = Country::getByIso($this->ebayProfile->getConfiguration(Common::COUNTRY_DEFAULT));
        $idCurrency = $this->ebayProfile->getConfiguration(Common::EBAY_CURRENCY);
        $specific_price_output = [];

        if (false == $idCurrency) {
            $idCurrency = Configuration::get('PS_CURRENCY_DEFAULT', null, null, $this->ebayProfile->id_shop);
        }

        if (false == $idCountry) {
            $idCountry = (int)Configuration::get('PS_COUNTRY_DEFAULT', null, null, $this->ebayProfile->id_shop);
        }

        $price = Product::priceCalculation(
            $this->ebayProfile->id_shop,//id_shop
            $idProduct,//id_product
            isset($params['id_product_attribute']) ? $params['id_product_attribute'] : null,//id_product_attribute
            $idCountry,//id_country
            isset($params['id_state']) ? $params['id_state'] : null,//id_state
            isset($params['postcode']) ? $params['postcode'] : null,//postcode
            $idCurrency,//id_currency
            isset($params['id_group']) ? $params['id_group'] : null,//id_group
            isset($params['quantity']) ? $params['quantity'] : 1,//quantity
            false,//use_tax
            isset($params['decimals']) ? $params['decimals'] : 6,//decimals
            isset($params['only_reduc']) ? $params['only_reduc'] : false,//only_reduc
            isset($params['use_reduc']) ? $params['use_reduc'] : true,//use_reduc
            isset($params['with_ecotax']) ? $params['with_ecotax'] : true,//with_ecotax
            $specific_price_output,//specific_price_output
            isset($params['use_group_reduction']) ? $params['use_group_reduction'] : true,//use_group_reduction
            isset($params['id_customer']) ? $params['id_customer'] : 0,//id_customer
            isset($params['use_customer_price']) ? $params['use_customer_price'] : true,//use_customer_price
            isset($params['id_cart']) ? $params['id_cart'] : 0,//id_cart
            isset($params['cart_quantity']) ? $params['cart_quantity'] : 0,//cart_quantity
            isset($params['id_customization']) ? $params['id_customization'] : null//id_customization
        );

        $taxRate = $this->productTax->getTaxRateByProduct($product);
        $price = $price * (1 + $taxRate / 100);
        $price = Tools::ps_round($price, $this->precision());

        return $price;
    }

    /**
     * @return int
     */
    protected function precision()
    {
        return 2;
    }
}
