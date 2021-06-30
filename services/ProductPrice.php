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

class ProductPrice
{
    protected $ebayProfile;

    protected $taxRate;

    public function __construct(EbayProfile $ebayProfile)
    {
        $this->ebayProfile = $ebayProfile;
        $this->taxRate = $this->getEbayProfileService()->getTaxRate($ebayProfile);
    }

    public function getPriceById($idProduct, $params = [])
    {
        $price = Product::getPriceStatic(
            (int)$idProduct,
            false,
            isset($params['id_product_attribute']) ? $params['id_product_attribute'] : null,
            isset($params['decimals']) ? $params['decimals'] : 6,
            isset($params['devisor']) ? $params['divisor'] : null,
            isset($params['only_reduc']) ? $params['only_reduc'] : false,
            isset($params['usereduc']) ? $params['usereduc'] : true,
            isset($params['quantity']) ? $params['quantity'] : 1,
            isset($params['force_associated_tax']) ? $params['force_associated_tax'] : false,
            isset($params['id_customer']) ? $params['id_customer'] : null,
            isset($params['id_cart']) ? $params['id_cart'] : null,
            isset($params['id_address']) ? $params['id_address'] : null,
            null,
            isset($params['with_ecotax']) ? $params['with_ecotax'] : true,
            isset($params['use_group_reduction']) ? $params['use_group_reduction'] : true,
            isset($params['context']) ? $params['context'] : null,
            isset($params['use_customer_price']) ? $params['use_customer_price'] : true,
            isset($params['id_customization']) ? $params['id_customization'] : null
        );

        $price = $price * (1 + $this->taxRate / 100);
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

    /**
     * @return EbayProfileService
     */
    protected function getEbayProfileService()
    {
        return new EbayProfileService();
    }
}
