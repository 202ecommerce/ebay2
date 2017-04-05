{*
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
*}
<?xml version="1.0" encoding="utf-8"?>
<AddFixedPriceItem xmlns="urn:ebay:apis:eBLBaseComponents">
	<ErrorLanguage>{$error_language|escape:'htmlall':'UTF-8'}</ErrorLanguage>
	<WarningLevel>High</WarningLevel>
	<Item>
		{if isset($sku)}
			<SKU>{$sku|escape:'htmlall':'UTF-8'}</SKU>
		{/if}
		{if isset($autopay)}
			<AutoPay>{$autopay|escape:'htmlall':'UTF-8'}</AutoPay>
		{/if}        
		<Title><![CDATA[{$title|cleanHtml}]]></Title>
		{if count($pictures)}
			<PictureDetails>
				<GalleryType>Gallery</GalleryType>
				{if $pictures|count > 1}
					<PhotoDisplay>PicturePack</PhotoDisplay>
				{/if}
				{foreach from=$pictures item=picture}
					<PictureURL>{$picture|escape:'htmlall':'UTF-8'}</PictureURL>
				{/foreach}
			</PictureDetails>
		{/if}
		<Description><![CDATA[{$description|ebayHtml}]]></Description>
		<PrimaryCategory>
			<CategoryID>{$category_id|escape:'htmlall':'UTF-8'}</CategoryID>
		</PrimaryCategory>



		<ConditionID>{if $condition_id > 0}{$condition_id|escape:'htmlall':'UTF-8'}{else}1000{/if}</ConditionID>
		{if $price_update && isset($start_price)}
			<StartPrice>{$start_price|escape:'htmlall':'UTF-8'}</StartPrice>
		{/if}
		<CategoryMappingAllowed>true</CategoryMappingAllowed>
		<Country>{$country|escape:'htmlall':'UTF-8'}</Country>
		<Currency>{$country_currency|escape:'htmlall':'UTF-8'}</Currency>
		<DispatchTimeMax>{$dispatch_time_max|escape:'htmlall':'UTF-8'}</DispatchTimeMax>
		<ListingDuration>{$listing_duration|escape:'htmlall':'UTF-8'}</ListingDuration>
		<ListingType>FixedPriceItem</ListingType>
		{if isset($pay_pal_email_address)}
		<PaymentMethods>PayPal</PaymentMethods>
		<PayPalEmailAddress>{$pay_pal_email_address|escape:'htmlall':'UTF-8'}</PayPalEmailAddress>
		{/if}
		<PostalCode>{$postal_code|escape:'htmlall':'UTF-8'}</PostalCode>
		{if isset($quantity)}
			<Quantity>{if $quantity < 0}0{else}{$quantity|escape:'htmlall':'UTF-8'}{/if}</Quantity>
		{/if}
		<ItemSpecifics>
			{foreach from=$item_specifics key=name item=value}
				<NameValueList>
					<Name><![CDATA[{$name|cleanHtml}]]></Name>
					<Value><![CDATA[{$value|cleanHtml}]]></Value>
				</NameValueList>
			{/foreach}
		</ItemSpecifics>

		{if isset($ktype)}
		<ItemCompatibilityList>
			<Compatibility>
				<NameValueList>
					<Name>KType</Name>
					{foreach from=$ktype key=name item=value}

							<Value><![CDATA[{$value}]]></Value>

					{/foreach}
				</NameValueList>

			</Compatibility>
		</ItemCompatibilityList>
		{/if}

		{$return_policy|cleanHtml}
        {if isset($variations)}
            {$variations|cleanHtml}
		{elseif isset($product_listing_details)}
            {$product_listing_details|cleanHtml}
        {/if}
		{if isset($shipping_details)}
		<ShippingDetails>{$shipping_details|cleanHtml}</ShippingDetails>
		{/if}
		{$buyer_requirements_details|cleanHtml}
		<Site>{$site|escape:'htmlall':'UTF-8'}</Site>
        
        {if isset($price_original)}
            <DiscountPriceInfo>
                <OriginalRetailPrice>{$price_original|escape:'htmlall':'UTF-8'}</OriginalRetailPrice>
                <SoldOffeBay>true</SoldOffeBay>
            </DiscountPriceInfo>
        {/if}
        
        {if isset($ebay_store_category_id)}
            <Storefront>
                  <StoreCategoryID>{$ebay_store_category_id|escape:'htmlall':'UTF-8'}</StoreCategoryID>
                  {*<!--<StoreCategoryName> string </StoreCategoryName>-->*}
            </Storefront>
        {/if}
	</Item>
	<RequesterCredentials>
		<eBayAuthToken>{$ebay_auth_token|escape:'htmlall':'UTF-8'}</eBayAuthToken>
	</RequesterCredentials>
</AddFixedPriceItem>
