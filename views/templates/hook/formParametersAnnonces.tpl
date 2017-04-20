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


<form action="{$url|escape:'htmlall':'UTF-8'}" method="post" class="form" id="configForm1">

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
		<legend><span data-dialoghelp="http://sellerupdate.ebay.co.uk/autumn2013/picture-standards" data-inlinehelp="{l s='Select the size of your main photo and any photos you want to include in your description. Go to Preferences> images. Your images must comply with eBay’s photo standards.' mod='ebay'}">{l s='Photo sizes' mod='ebay'}</span></legend>

		<label>
			{l s='Default photo' mod='ebay'}
		</label>
		<div class="margin-form">
			<select name="sizedefault" data-inlinehelp="{l s='This will be the main photo and will appear on the search result and item pages.' mod='ebay'}" class="ebay_select">
				{if isset($sizes) && $sizes && sizeof($sizes)}
					{foreach from=$sizes item='size'}
						<option value="{$size.id_image_type|escape:'htmlall':'UTF-8'}"{if $size.id_image_type == $sizedefault} selected{/if}>{$size.name|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				{/if}
			</select>
		</div>
		<div class="clear both"></div>

		<label>
			{l s='Main photo' mod='ebay'}
		</label>
		<div class="margin-form">
			<select name="sizebig" data-inlinehelp="{l s='This photo will appear as default photo in your listing\'s description.' mod='ebay'}" class="ebay_select">
				{if isset($sizes) && $sizes && sizeof($sizes)}
					{foreach from=$sizes item='size'}
						<option value="{$size.id_image_type|escape:'htmlall':'UTF-8'}"{if $size.id_image_type == $sizebig} selected{/if}>{$size.name|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				{/if}
			</select>
		</div>
		<div class="clear both"></div>

		<label>
			{l s='Small photo' mod='ebay'}
		</label>
		<div class="margin-form">
			<select name="sizesmall" data-inlinehelp="{l s='This photo will appear as thumbnail in your listing\'s description.' mod='ebay'}" class="ebay_select">
				{if isset($sizes) && $sizes && sizeof($sizes)}
					{foreach from=$sizes item='size'}
						<option value="{$size.id_image_type|escape:'htmlall':'UTF-8'}"{if $size.id_image_type == $sizesmall} selected{/if}>{$size.name|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				{/if}
			</select>
		</div>
		<div style="clear:both;"></div>

		<label>
			{l s='Number of additional pictures (0 will send one picture)' mod='ebay'}
		</label>
		<div class="margin-form">
			<input type="text" name="picture_per_listing" value="{$picture_per_listing|escape:'htmlall':'UTF-8'}" onchange="checkInputParameters()">
		</div>
		<div style="clear:both;"></div>
		<label>
			{l s='Send new product images on the next synchronization' mod='ebay'}
		</label>
		<div class="margin-form">
			<a id="reset-image" href="#" target="_blank" class="button">Active</a>
			<p id="reset-image-result"></p>
		</div>
		<div style="clear:both;"></div>
		<label>
			{l s='Do not send variations images' mod='ebay'}
		</label>
		<div class="margin-form">
			<input type="checkbox" name="picture_skip_variations" value="1" {if $picture_skip_variations} checked="checked"{/if}>
		</div>
		<div style="clear:both;"></div>
		<label>
			{l s='Default photo CHARACT' mod='ebay'}
		</label>
		<div class="margin-form">
			<select name="picture_charact_variations" data-inlinehelp="{l s='This will be the main photo and will appear on the search result and item pages.' mod='ebay'}" class="ebay_select">
				{if isset($itemspecifics) && $itemspecifics && sizeof($itemspecifics)}
					{foreach from=$itemspecifics item='itemspecific'}
						<option value="{$itemspecific.id_attribute_group|escape:'htmlall':'UTF-8'}"{if $itemspecific.id_attribute_group == $picture_charact_variations} selected{/if}>{$itemspecific.name|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				{/if}
			</select>
		</div>
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
