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

{if isset($relogin) && $relogin}
	{literal}
	<script>
		$(document).ready(function() {
			var win = window.location = '{/literal}{$redirect_url|escape:'UTF-8'}{literal}';
		});		

	</script>
	{/literal}
{/if}
<script type="text/javascript">
	var id_shop = '{$id_shop|escape:'htmlall':'UTF-8'}';
	var tooltip_numeric = "{l s='You must enter a number.' mod='ebay'}";
	var tooltip_max_pictures = "{l s='The maximum number is 12, so you can put up to 11 in this field.' mod='ebay'}";

	$(document).ready(function(){ldelim}
		if(regenerate_token_show)
		{ldelim}
			$('.regenerate_token_button').show();
			$('.regenerate_token_button label').css('color', 'red').html("{l s='You must regenerate your authentication token' mod='ebay'}");
			$('.regenerate_token_click').hide();
		{rdelim}
		$('.regenerate_token_click span').click(function()
		{ldelim}
			$('.regenerate_token_button').show();
			$('.regenerate_token_click').hide();
		{rdelim});
	})
</script>

	{if isset($check_token_tpl)}
	<fieldset id="regenerate_token">
		<legend>{l s='Token' mod='ebay'}</legend>
			{$check_token_tpl|ebayHtml}
	</fieldset>	
	{/if}
	
<form action="{$url|escape:'htmlall':'UTF-8'}" method="post" class="form" id="configForm1">
    
	<fieldset style="margin-top:10px;">
		<legend>{l s='Account details' mod='ebay'}</legend>
		<h4>{l s='To list your products on eBay, you need to create' mod='ebay'} <a href="https://www.paypal.com/" target="_blank">{l s='a PayPal account.' mod='ebay'}</a></h4>
        
		<input type="hidden" name="ebay_shop" value="{$ebayShopValue|escape:'htmlall':'UTF-8'}" />            
        
		<label>{l s='Paypal email address' mod='ebay'} : </label>
		<div class="margin-form">

			<input type="text" size="20" name="ebay_paypal_email" value="{$ebay_paypal_email|escape:'htmlall':'UTF-8'}"/>
			<p>{l s='You have to set your PayPal e-mail account, this is the only payment solution available with this module.' mod='ebay'}<a class="kb-help" data-errorcode="{$help.code_payment_solution|escape:'htmlall':'UTF-8'}" data-module="ebay" data-lang="{$help.lang|escape:'htmlall':'UTF-8'}" module_version="{$help.module_version|escape:'htmlall':'UTF-8'}" prestashop_version="{$help.ps_version|escape:'htmlall':'UTF-8'}" href="" target="_blank"></a></p>
		</div>
        
		<label>
			{l s='Currency' mod='ebay'}
		</label>
		<div class="margin-form">
			<select name="currency" data-inlinehelp="{l s='This currency will be used for your products sold on eBay' mod='ebay'}" class="ebay_select">
				{if isset($currencies) && $currencies && sizeof($currencies)}
					{foreach from=$currencies item='currency'}
						<option value="{$currency.id_currency|escape:'htmlall':'UTF-8'}"{if $currency.id_currency == $current_currency} selected{/if}>{$currency.name|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				{/if}
			</select>
		</div>        
        
		<label>{l s='Item location' mod='ebay'} : </label>
		<div class="margin-form">
			<input type="text" size="20" name="ebay_shop_postalcode" value="{$shopPostalCode|escape:'htmlall':'UTF-8'}"/>
			<p>{l s='Your shop\'s postal code' mod='ebay'}</p>
		</div>
		<label>{l s='Item Country' mod='ebay'} : </label>
		<div class="margin-form">
			<select name="ebay_shop_country" class="ebay_select">
                <option value=""></option>
			{foreach from=$ebay_shop_countries item=ebay_shop_country}
				<option value="{$ebay_shop_country.iso_code|escape:'htmlall':'UTF-8'}" {if $current_ebay_shop_country == $ebay_shop_country.iso_code} selected="selected"{/if}>{$ebay_shop_country.site_name|escape:'htmlall':'UTF-8'}</option>
			{/foreach}							   
			</select>            
			<p>{l s='Your shop\'s country' mod='ebay'}</p>
		</div>     
		<label>
			{l s='Immediate Payment' mod='ebay'}
		</label>
		<div class="margin-form">
			<input type="checkbox" name="immediate_payment" value="1"{if $immediate_payment} checked="checked"{/if}>
		</div>           

		<div class="show regenerate_token_click" style="display:block;text-align:center;cursor:pointer">
			<span data-inlinehelp="{l s='Use only if you get a message saying that your authentication is expired.' mod='ebay'}">{l s='Click here to generate a new authentication token.' mod='ebay'}</span>
		</div>
		<div class="hide regenerate_token_button" style="display:none;">
			<label>{l s='Regenerate Token' mod='ebay'} :</label>
			<a href="{$url|escape:'htmlall':'UTF-8'}&action=regenerate_token">
				<input type="button" id="token-btn" class="button" value="{l s='Regenerate Token' mod='ebay'}" />
			</a>
		</div>
	</fieldset>

    <fieldset style="margin-top:10px;">
       
        <legend>{l s='EAN Sync' mod='ebay'} <a class="kb-help" data-errorcode="{$help_ean.error_code}" data-module="ebay" data-lang="{$help_ean.lang}" module_version="{$help_ean.module_version}" prestashop_version="{$help_ean.ps_version}" href="" target="_blank"></a></legend>

        <label>{l s='Synchronize EAN with :' mod='ebay'}</label>
        <div class="margin-form">
            <select name="synchronize_ean" class="ebay_select">
                <option value="">{l s='Do not synchronise' mod='ebay'}</option>
                <option value="EAN"{if "EAN" == $synchronize_ean} selected{/if}>{l s='EAN' mod='ebay'}</option>
                {*<option value="SUP_REF"{if "SUP_REF" == $synchronize_ean} selected{/if}>{l s='Supplier Reference' mod='ebay'}</option>*}
                <option value="REF"{if "REF" == $synchronize_ean} selected{/if}>{l s='Reference' mod='ebay'}</option>
                <option value="UPC"{if "UPC" == $synchronize_ean} selected{/if}>{l s='UPC' mod='ebay'}</option>
            </select>
        </div>
        <label>{l s='Synchronize MPN with :' mod='ebay'}</label>
        <div class="margin-form">
            <select name="synchronize_mpn" class="ebay_select">
                <option value="">{l s='Do not synchronise' mod='ebay'}</option>
                <option value="EAN"{if "EAN" == $synchronize_mpn} selected{/if}>{l s='EAN' mod='ebay'}</option>
                {*<option value="SUP_REF"{if "SUP_REF" == $synchronize_mpn} selected{/if}>{l s='Supplier Reference' mod='ebay'}</option>*}
                <option value="REF"{if "REF" == $synchronize_mpn} selected{/if}>{l s='Reference' mod='ebay'}</option>
                <option value="UPC"{if "UPC" == $synchronize_mpn} selected{/if}>{l s='UPC' mod='ebay'}</option>
            </select>
        </div>
        <label>{l s='Synchronize UPC with :' mod='ebay'}</label>
        <div class="margin-form">
            <select name="synchronize_upc" class="ebay_select">
                <option value="">{l s='Do not synchronise' mod='ebay'}</option>
                <option value="EAN"{if "EAN" == $synchronize_upc} selected{/if}>{l s='EAN' mod='ebay'}</option>
                {*<option value="SUP_REF"{if "SUP_REF" == $synchronize_upc} selected{/if}>{l s='Supplier Reference' mod='ebay'}</option>*}
                <option value="REF"{if "REF" == $synchronize_upc} selected{/if}>{l s='Reference' mod='ebay'}</option>
                <option value="UPC"{if "UPC" == $synchronize_upc} selected{/if}>{l s='UPC' mod='ebay'}</option>
            </select>
        </div>
        <label>{l s='Synchronize ISBN with :' mod='ebay'}</label>
        <div class="margin-form">
            <select name="synchronize_isbn" class="ebay_select">
                <option value="">{l s='Do not synchronise' mod='ebay'}</option>
                <option value="EAN"{if "EAN" == $synchronize_isbn} selected{/if}>{l s='EAN' mod='ebay'}</option>
                {*<option value="SUP_REF"{if "SUP_REF" == $synchronize_isbn} selected{/if}>{l s='Supplier Reference' mod='ebay'}</option>*}
                <option value="REF"{if "REF" == $synchronize_isbn} selected{/if}>{l s='Reference' mod='ebay'}</option>
                <option value="UPC"{if "UPC" == $synchronize_isbn} selected{/if}>{l s='UPC' mod='ebay'}</option>
            </select>
        </div>
        {* <label>
            {l s='Option \'Does not apply\'' mod='ebay'}
        </label>
        <div class="margin-form">
            <input type="checkbox" name="ean_not_applicable" value="1"{if $ean_not_applicable} checked="checked"{/if} data-inlinehelp="{l s='If you check this box, the module will send EAN value &quot;Does not apply&quot; when none of EAN, ISBN or UPC is set.' mod='ebay'}">
        </div> *}

        <div style="clear:both;"></div>
        
    </fieldset>
        
   <fieldset style="margin-top:10px;">
		<legend>{l s='Returns policy' mod='ebay'}</legend>
		<label>{l s='Please define your returns policy' mod='ebay'} : </label>
		<div class="margin-form">
			<select name="ebay_returns_accepted_option" data-dialoghelp="#returnsAccepted" data-inlinehelp="{l s='eBay business sellers must accept returns under the Distance Selling Regulations.' mod='ebay'}" class="ebay_select">
			{foreach from=$policies item=policy}
				<option value="{$policy.value|escape:'htmlall':'UTF-8'}" {if $returnsConditionAccepted == $policy.value} selected="selected"{/if}>{$policy.description|escape:'htmlall':'UTF-8'}</option>
			{/foreach}							   
			</select>
		</div>
		<div style="clear:both;"></div>
		<label>{l s='Returns within' mod='ebay'} :</label>
		<div class="margin-form">
			<select name="returnswithin" data-inlinehelp="{l s='eBay business sellers must offer a minimum of 14 days for buyers to return their items.' mod='ebay'}" class="ebay_select">
					{if isset($within_values) && $within_values && sizeof($within_values)}
						{foreach from=$within_values item='within_value'}
							<option value="{$within_value.value|escape:'htmlall':'UTF-8'}"{if isset($within) && $within == $within_value.value} selected{/if}>{$within_value.description|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					{/if}
			</select>
		</div>
		<div style="clear:both;"></div>
		<label>{l s='Who pays' mod='ebay'} :</label>
		<div class="margin-form">
			<select name="returnswhopays" class="ebay_select">
				{if isset($whopays_values) && $whopays_values && sizeof($whopays_values)}
					{foreach from=$whopays_values item='whopays_value'}
						<option value="{$whopays_value.value|escape:'htmlall':'UTF-8'}"{if isset($whopays) && $whopays == $whopays_value.value} selected{/if}>{$whopays_value.description|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				{/if}
			</select>
		</div>
		<label>{l s='Any other information' mod='ebay'} : </label>
		<div class="margin-form">
			<textarea name="ebay_returns_description" cols="120" rows="10" data-inlinehelp="{l s='This description will be displayed in the returns policy section of the listing page.' mod='ebay'}">{$ebayReturns|cleanHtml}</textarea>
		</div>
	</fieldset>
             
	
    <fieldset style="margin-top:10px;">
 		<legend>{l s='Order Synchronization from PrestaShop to eBay' mod='ebay'}</legend>
		<label>
			{l s='Send tracking code' mod='ebay'}
		</label>
		<div class="margin-form">
			<input type="checkbox" name="send_tracking_code" value="1"{if $send_tracking_code} checked="checked"{/if}>
		</div>
        
		<label>
			{l s='Status used to indicate product has been shipped' mod='ebay'}
		</label>
		<div class="margin-form">
			<select name="shipped_order_state" class="ebay_select" >
                <option value=""></option>
				{if isset($order_states) && $order_states && sizeof($order_states)}
					{foreach from=$order_states item='order_state'}
						<option value="{$order_state.id_order_state|escape:'htmlall':'UTF-8'}"{if $order_state.id_order_state == $current_order_state} selected{/if}>{$order_state.name|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				{/if}
			</select>
		</div></br>
		{if $help_ean.ps_version > '1.4.11'}
		<label>
			{l s='Status used to indicate order has been canceled' mod='ebay'}
		</label>
		<div class="margin-form">
			<select name="return_order_state" class="ebay_select" data-inlinehelp="{l s='Synchronisation is 2 ways : a cancellation validated on eBay will change PrestaShop order to this status, and a change to this status on PrestaShop will cancel order on eBay.' mod='ebay'}">
				<option value=""></option>
				{if isset($order_states) && $order_states && sizeof($order_states)}
					{foreach from=$order_states item='order_state'}
						<option value="{$order_state.id_order_state|escape:'htmlall':'UTF-8'}"{if $order_state.id_order_state == $current_order_return_state} selected{/if}>{$order_state.name|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				{/if}
			</select>
		</div>
		{/if}
		<div style="clear:both;"></div>
     </fieldset>    
     
     
	<!-- Listing Durations -->
	<fieldset style="margin-top:10px;">
		<legend>{l s='Listing Duration' mod='ebay'}</legend>
		
		<label>
			{l s='Listing duration' mod='ebay'}
		</label>
		<div class="margin-form">

			<select name="listingdurations"  class="ebay_select">
				{foreach from=$listingDurations item=listing key=key}
					<option value="{$key|escape:'htmlall':'UTF-8'}" {if $ebayListingDuration == $key}selected="selected" {/if}>{$listing|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
			</select><a class="kb-help" data-errorcode="{$help_gtc.error_code}" data-module="ebay" data-lang="{$help_gtc.lang}" module_version="{$help_gtc.module_version}" prestashop_version="{$help_gtc.ps_version}" href="" target="_blank"></a>
		</div>
		
        <label for="">{l s='Automatic relist' mod='ebay'}</label>
		<div class="margin-form"><input type="checkbox" name="automaticallyrelist" {if $automaticallyRelist == 'on'} checked="checked" {/if} /></div>
		<label>
			{l s='Out of Stock status ' mod='ebay'}
		</label>
		<div class="margin-form">
			<input type="text" readonly value="{if $out_of_stock_value}{l s='Activated' mod='ebay'}{else}{l s='Deactivated' mod='ebay'}{/if}" style="text-align: center;">
			<a class="kb-help" data-errorcode="{$help_out_of_stock.error_code}" data-module="ebay" data-lang="{$help_out_of_stock.lang}" module_version="{$help_out_of_stock.module_version}" prestashop_version="{$help_out_of_stock.ps_version}" href="" target="_blank"></a>

		</div>
	</fieldset>
    
        
	<div class="margin-form" id="buttonEbayParameters" style="margin-top:5px;">
		<a href="#categoriesProgression" {if $catLoaded}id="displayFancybox"{/if}>
			<input class="primary button" name="submitSave" type="hidden" value="{l s='Save and continue' mod='ebay'}" />
			<input class="primary button" type="submit" id="save_ebay_parameters" value="{l s='Save and continue' mod='ebay'}" />
		</a>
	</div>

	<div id="ebayreturnshide" style="display:none;">{$ebayReturns|escape:'htmlall':'UTF-8'}</div>
</form>
