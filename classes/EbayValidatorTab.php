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

class EbayValidatorTab
{

    public static function getShippingTabConfiguration($id_ebay_profile)
    {
        $ebay = new Ebay();
        $shipping_national = EbayShipping::getNationalShippings($id_ebay_profile);
        if (!is_array($shipping_national) || count($shipping_national) == 0) {
            return array(
                'indicator'    => 'wrong',
                'indicatorBig' => 'wrong',
                'message'      => $ebay->l('You must at least configure one domestic shipping service', 'ebayvalidatortab'),
            );
        }

        $shipping_international = EbayShipping::getInternationalShippings($id_ebay_profile);
        if (!EbayShipping::internationalShippingsHaveZone($shipping_international)) {
            return array(
                'indicator'    => 'wrong',
                'indicatorBig' => 'wrong',
                'message'      => $ebay->l('Your international shipping must at least have one zone configured', 'ebayvalidatortab'),
            );
        }

        if (count($shipping_international) == 0) {
            return array(
                'indicator'    => 'success',
                'indicatorBig' => 'mind',
                'message'      => $ebay->l('You could benefit to configure international shipping services', 'ebayvalidatortab'),
            );
        }

        return array(
            'indicator' => 'success',
        );
    }

    public static function getParametersTabConfiguration($id_ebay_profile)
    {
        $configs_mandatory_profile = array('EBAY_PAYPAL_EMAIL', 'EBAY_SHOP_POSTALCODE');
        //$configs_mandatory = array('EBAY_API_USERNAME');
        $ebay = new Ebay();
        $ebay_profile = new EbayProfile($id_ebay_profile);

        $has_something_configured = false;
        $return_message = null;
        foreach ($configs_mandatory_profile as $config) {
            if (($ebay_profile->getConfiguration($config)) == null) {
                if (!$return_message) {
                    $return_message = array(
                        'indicator'    => 'wrong',
                        'indicatorBig' => 'wrong',
                        'message'      => $ebay->l('Your need to configure the field ', 'ebayvalidatortab').' '.$config,
                    );
                }

            } else {
                $has_something_configured = true;
            }

        }

        // if nothing is configured, we don't show a message
        if ($has_something_configured && $return_message) {
            return $return_message;
        }

        /*
        foreach ($configs_mandatory as $config)
        {
        if((Configuration::get($config)) == null)
        return array(
        'indicator' => 'wrong',
        'indicatorBig' => 'wrong',
        'message' => $ebay->l('Your need to configure the field ', 'ebayvalidatortab') . $config
        );
        }
         */
        if (!$ebay_profile->ebay_user_identifier) {
            return array(
                'indicator'    => 'wrong',
                'indicatorBig' => 'wrong',
                'message'      => $ebay->l('Your need to configure the field ', 'ebayvalidatortab').' ebay user identifier',
            );
        }

        return array(
            'indicator' => 'success',
        );
    }

    public static function getCategoryTabConfiguration($id_ebay_profile)
    {
        return array(
            'indicator' => 'success',
        );
    }

    public static function getItemSpecificsTabConfiguration($id_ebay_profile)
    {
        //Check if all mandatory items specifics have been configured
        $ebay = new Ebay();
        if (!EbayCategorySpecific::allMandatorySpecificsAreConfigured($id_ebay_profile)) {
            return array(
                'indicator'    => 'wrong',
                'indicatorBig' => 'wrong',
                'message'      => $ebay->l('You need to configure your mandatory items specifics ', 'ebayvalidatortab'),
            );
        }

        //Check if optional items specifics have been configured
        if (!EbayCategorySpecific::atLeastOneOptionalSpecificIsConfigured($id_ebay_profile)) {
            return array(
                'indicator'    => 'success',
                'indicatorBig' => 'mind',
                'message'      => $ebay->l('You could gain visibility by configuring optional items specifics ', 'ebayvalidatortab'),
            );
        }

        return array(
            'indicator' => 'success',
        );
    }

    public static function getTemplateTabConfiguration($id_ebay_profile)
    {
        $ebay_profile = new EbayProfile($id_ebay_profile);
        $ebay = new Ebay();

        if ($ebay_profile->getConfiguration('EBAY_PRODUCT_TEMPLATE_TITLE') == '') {
            return array(
                'indicator'    => 'wrong',
                'indicatorBig' => 'wrong',
                'message'      => $ebay->l('You need to add something in your template title. Use the tags available to personnalize your product title on eBay', 'ebayvalidatortab'),
            );
        }

        if ($ebay_profile->getConfiguration('EBAY_PRODUCT_TEMPLATE_TITLE') == '{TITLE}') {
            return array(
                'indicator'    => 'success',
                'indicatorBig' => 'mind',
                'message'      => $ebay->l('You could improve your title template by adding informations about the items', 'ebayvalidatortab'),
            );
        }

        return array(
            'indicator' => 'success',
        );

    }

    public static function getSynchronisationTabConfiguration($id_ebay_profile)
    {
        return array(
            'indicator' => 'success',
        );
    }

    public static function getEbayListingTabConfiguration($id_ebay_profile)
    {
        return array(
            'indicator' => 'success',
        );
    }
    public static function getEbayBusinessPoliciesTabConfiguration($id_ebay_profile)
    {

        if (EbayConfiguration::get($id_ebay_profile, 'EBAY_BUSINESS_POLICIES_CONFIG') == 0 && EbayConfiguration::get($id_ebay_profile, 'EBAY_BUSINESS_POLICIES')==1) {
            return array(
                'indicator'    => 'wrong',
            );
        }

        
        return array(
            'indicator' => 'success',
        );
    }
}
