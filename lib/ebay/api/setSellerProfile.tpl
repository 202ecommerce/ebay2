{*
* 2007-2022 PrestaShop
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
* @copyright Copyright (c) 2007-2022 202-ecommerce
* @license Commercial license
*  International Registered Trademark & Property of PrestaShop SA
*}

<setSellerProfileRequest xmlns="http://www.ebay.com/marketplace/selling/v1/services">
    <shippingPolicyProfile>
        <categoryGroups>
            <categoryGroup>
                <default>false</default>
                <name>ALL</name>
            </categoryGroup>
        </categoryGroups>
        <profileId>{$shipping_id}</profileId>
        <profileName>{$shipping_name}</profileName>
        <profileDesc>{$description}</profileDesc>
        <profileType>SHIPPING</profileType>
        <shippingPolicyInfo>
            <dispatchTimeMax>{$dispatch_time_max}</dispatchTimeMax>
            {foreach from=$national_services key=service_name item=services}
                {foreach from=$services item=service}
                    {if $service.serviceCosts !== false}

                        <domesticShippingPolicyInfoService>
                            <freeShipping>false</freeShipping>
                            <shippingService>{$service_name|escape:'htmlall':'UTF-8'}</shippingService>
                            <shippingServiceAdditionalCost>{$service.serviceAdditionalCosts|escape:'htmlall':'UTF-8'}</shippingServiceAdditionalCost>
                            <shippingServiceCost>{$service.serviceCosts|escape:'htmlall':'UTF-8'}</shippingServiceCost>

                        </domesticShippingPolicyInfoService>

                    {/if}
                {/foreach}
            {/foreach}
            {if !empty($international_services)}


                {foreach from=$international_services key=service_name item=services}
                    {foreach from=$services item=service}
                        {if $service.serviceCosts !== false}
                            <intlShippingPolicyInfoService>
                                <freeShipping>false</freeShipping>
                                <shippingService>{$service_name|escape:'htmlall':'UTF-8'}</shippingService>
                                <shippingServiceAdditionalCost>{$service.serviceAdditionalCosts|escape:'htmlall':'UTF-8'}</shippingServiceAdditionalCost>
                                <shippingServiceCost>{$service.serviceCosts|escape:'htmlall':'UTF-8'}</shippingServiceCost>
                                {foreach from=$service.locationsToShip item=location}
                                    <shipToLocation>{$location.id_ebay_zone|escape:'htmlall':'UTF-8'}</shipToLocation>
                                {/foreach}
                            </intlShippingPolicyInfoService>

                        {/if}
                    {/foreach}
                {/foreach}
                {if empty($excluded_zones)}
                    <excludeShipToLocation>NONE</excludeShipToLocation>
                {else}
                    {foreach from=$excluded_zones item=zone}
                        <excludeShipToLocation>{$zone.location|escape:'htmlall':'UTF-8'}</excludeShipToLocation>
                    {/foreach}
                {/if}
            {/if}
            <shippingPolicyCurrency>{$currency_id|escape:'htmlall':'UTF-8'}</shippingPolicyCurrency>
            <domesticShippingType>Flat</domesticShippingType>
            {if !empty($international_services)}
            <intlShippingType>Flat</intlShippingType>
            {/if}
        </shippingPolicyInfo>

        <shippingPolicyName>{$shipping_name}</shippingPolicyName>
        <siteId>{$ebay_site_id}</siteId>
    </shippingPolicyProfile>

</setSellerProfileRequest>
