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
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<?xml version="1.0" encoding="utf-8"?>
<ReviseFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
	<ErrorLanguage>{$error_language|escape:'htmlall':'UTF-8'}</ErrorLanguage>
	<WarningLevel>High</WarningLevel>
	<Item>
		<ItemID>{$item_id|escape:'htmlall':'UTF-8'}</ItemID>
		{if isset($country)}
			<Country>{$country|escape:'htmlall':'UTF-8'}</Country>
			<Location>{$country|escape:'htmlall':'UTF-8'}</Location>
		{/if}
		{if isset($autopay)}
			<AutoPay>{$autopay|escape:'htmlall':'UTF-8'}</AutoPay>
		{/if}        
		{if isset($country_currency)}
			<Currency>{$country_currency|escape:'htmlall':'UTF-8'}</Currency>
		{/if}
		<ConditionID>{if $condition_id > 0}{$condition_id|escape:'htmlall':'UTF-8'}{else}1000{/if}</ConditionID>
		{if isset($listing_type)}
			<ListingType>{$listing_type|escape:'htmlall':'UTF-8'}</ListingType>
		{/if}
		{if isset($payment_method) && !$bp_active}
			<PaymentMethods>{$payment_method|escape:'htmlall':'UTF-8'}</PaymentMethods>
			<PayPalEmailAddress>{$pay_pal_email_address|escape:'htmlall':'UTF-8'}</PayPalEmailAddress>
		{/if}
		{if isset($postal_code)}
			<PostalCode>{$postal_code|escape:'htmlall':'UTF-8'}</PostalCode>
		{/if}
		{if isset($category_id)}
			<PrimaryCategory>
				<CategoryID>{$category_id|escape:'htmlall':'UTF-8'}</CategoryID>
			</PrimaryCategory>		
		{/if}

		{if isset($pictures)&& count($pictures)}
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
		{if $sku && isset($sku) && !$variations}
			<SKU>{$sku|escape:'htmlall':'UTF-8'}</SKU>
		{/if}
		<ListingDuration>{$listing_duration|escape:'htmlall':'UTF-8'}</ListingDuration>
		{if isset($quantity)  && !$variations}
			<Quantity>{if $quantity < 0}0{else}{$quantity|escape:'htmlall':'UTF-8'}{/if}</Quantity>
		{/if}
		{if $price_update && isset($start_price) && $start_price}
			<StartPrice>{$start_price|escape:'htmlall':'UTF-8'}</StartPrice>
		{/if}
		{if isset($resynchronize) && $resynchronize}
			<DispatchTimeMax>{$dispatch_time_max|escape:'htmlall':'UTF-8'}</DispatchTimeMax>
			<Title><![CDATA[{$title|unescape:"UTF-8" nofilter}]]></Title>
			<Description><![CDATA[{$description nofilter}]]></Description>
		
			
		{if isset($shipping_details) && !$bp_active}
			<ShippingDetails>{$shipping_details nofilter}</ShippingDetails>
		{/if}
			{$buyer_requirements_details nofilter}
		{/if}
			<ItemSpecifics>
				{foreach from=$item_specifics key=name item=value}
					<NameValueList>
						<Name><![CDATA[{$name nofilter}]]></Name>
						{if $value|is_array}
							{foreach $value as $item}
								{if $item != ""}
									<Value><![CDATA[{$item}]]></Value>
								{/if}
							{/foreach}
						{else}
							<Value><![CDATA[{$value}]]></Value>
						{/if}
					</NameValueList>
				{/foreach}
            </ItemSpecifics>
		
		{if isset($isKtype)}
			{if $isKtype}
				<ItemCompatibilityList>
					{if isset($ktype)}
					{foreach from=$ktype key=name item=value}
					<Compatibility>
						<NameValueList>
							<Name>KType</Name>


								<Value><![CDATA[{$value}]]></Value>

						</NameValueList>

					</Compatibility>

					{/foreach}
					{/if}
					<ReplaceAll>TRUE</ReplaceAll>
				</ItemCompatibilityList>
			{/if}
		{/if}
		
        {$return_policy nofilter}
		
        {if isset($site)}
            <Site>{$site|escape:'htmlall':'UTF-8'}</Site>
		{/if}
        {if isset($variations) && $variations}
            {$variations nofilter}
		{elseif isset($product_listing_details)}
            {$product_listing_details nofilter}
        {/if}

        {if isset($price_original)}
            <DiscountPriceInfo>
                <OriginalRetailPrice>{$price_original|escape:'htmlall':'UTF-8'}</OriginalRetailPrice>
                <SoldOffeBay>true</SoldOffeBay>
            </DiscountPriceInfo>
        {/if}

        {if isset($ebay_store_category_id)}
            <Storefront>
                  <StoreCategoryID>{$ebay_store_category_id|escape:'htmlall':'UTF-8'}</StoreCategoryID>
                  <!--<StoreCategoryName> string </StoreCategoryName>-->
            </Storefront>
        {/if} 
	</Item>
	<RequesterCredentials>
		<eBayAuthToken>{$ebay_auth_token|escape:'htmlall':'UTF-8'}</eBayAuthToken>
	</RequesterCredentials>
</ReviseFixedPriceItemRequest>
