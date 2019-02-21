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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


<form action="{$url|escape:'htmlall':'UTF-8'}" method="post" class="form form-horizontal panel" >

	<fieldset>
		<div class="panel-heading">
			{l s='EAN Sync' mod='ebay'} <a class="kb-help" data-errorcode="{$help_ean.error_code}" data-module="ebay" data-lang="{$help_ean.lang}" module_version="{$help_ean.module_version}" prestashop_version="{$help_ean.ps_version}" href="" target="_blank">&nbsp;<i class="icon-info-circle"></i></a>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Synchronize EAN with' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<select name="synchronize_ean" class="form-control">
					<option value="">{l s='Do not synchronise' mod='ebay'}</option>
					<option value="EAN"{if "EAN" == $synchronize_ean} selected{/if}>{l s='EAN' mod='ebay'}</option>
					{*<option value="SUP_REF"{if "SUP_REF" == $synchronize_ean} selected{/if}>{l s='Supplier Reference' mod='ebay'}</option>*}
					<option value="REF"{if "REF" == $synchronize_ean} selected{/if}>{l s='Reference' mod='ebay'}</option>
					<option value="UPC"{if "UPC" == $synchronize_ean} selected{/if}>{l s='UPC' mod='ebay'}</option>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Synchronize MPN with' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<select name="synchronize_mpn" class="form-control">
					<option value="">{l s='Do not synchronise' mod='ebay'}</option>
					<option value="EAN"{if "EAN" == $synchronize_mpn} selected{/if}>{l s='EAN' mod='ebay'}</option>
					{*<option value="SUP_REF"{if "SUP_REF" == $synchronize_mpn} selected{/if}>{l s='Supplier Reference' mod='ebay'}</option>*}
					<option value="REF"{if "REF" == $synchronize_mpn} selected{/if}>{l s='Reference' mod='ebay'}</option>
					<option value="UPC"{if "UPC" == $synchronize_mpn} selected{/if}>{l s='UPC' mod='ebay'}</option>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Synchronize UPC with' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<select name="synchronize_upc" class="form-control">
					<option value="">{l s='Do not synchronise' mod='ebay'}</option>
					<option value="EAN"{if "EAN" == $synchronize_upc} selected{/if}>{l s='EAN' mod='ebay'}</option>
					{*<option value="SUP_REF"{if "SUP_REF" == $synchronize_upc} selected{/if}>{l s='Supplier Reference' mod='ebay'}</option>*}
					<option value="REF"{if "REF" == $synchronize_upc} selected{/if}>{l s='Reference' mod='ebay'}</option>
					<option value="UPC"{if "UPC" == $synchronize_upc} selected{/if}>{l s='UPC' mod='ebay'}</option>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Synchronize ISBN with' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<select name="synchronize_isbn" class="form-control">
					<option value="">{l s='Do not synchronise' mod='ebay'}</option>
					<option value="EAN"{if "EAN" == $synchronize_isbn} selected{/if}>{l s='EAN' mod='ebay'}</option>
					{*<option value="SUP_REF"{if "SUP_REF" == $synchronize_isbn} selected{/if}>{l s='Supplier Reference' mod='ebay'}</option>*}
					<option value="REF"{if "REF" == $synchronize_isbn} selected{/if}>{l s='Reference' mod='ebay'}</option>
					<option value="UPC"{if "UPC" == $synchronize_isbn} selected{/if}>{l s='UPC' mod='ebay'}</option>
				</select>
			</div>
		</div>
		{* <label>
			{l s='Option \'Does not apply\'' mod='ebay'}
		</label>
		<div class="margin-form">
			<input type="checkbox" name="ean_not_applicable" value="1"{if $ean_not_applicable} checked="checked"{/if} data-inlinehelp="{l s='If you check this box, the module will send EAN value &quot;Does not apply&quot; when none of EAN, ISBN or UPC is set.' mod='ebay'}">
		</div> *}
		<div class="clearfix"></div>
	</fieldset>

   <fieldset style="margin-top:10px;">
		<div class="panel-heading">{l s='Returns policy' mod='ebay'}</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				<span title="{l s='eBay business sellers must accept returns under the Distance Selling Regulations.' mod='ebay'}" class="label-tooltip" data-toggle="tooltip">
					{l s='Please define your returns policy' mod='ebay'}
				</span>
			</label>
			<div class="col-sm-9">
				<select name="ebay_returns_accepted_option" class="form-control">
				{foreach from=$policies item=policy}
					<option value="{$policy.value|escape:'htmlall':'UTF-8'}" {if $returnsConditionAccepted == $policy.value} selected="selected"{/if}>{$policy.description|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				<span title="{l s='eBay business sellers must offer a minimum of 14 days for buyers to return their items.' mod='ebay'}" class="label-tooltip" data-toggle="tooltip">
					{l s='Returns within' mod='ebay'}
				</span>
			</label>
			<div class="col-sm-9">
				<select name="returnswithin" class="form-control">
						{if isset($within_values) && $within_values && sizeof($within_values)}
							{foreach from=$within_values item='within_value'}
								<option value="{$within_value.value|escape:'htmlall':'UTF-8'}"{if isset($within) && $within == $within_value.value} selected{/if}>{$within_value.description|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						{/if}
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Who pays' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<select name="returnswhopays" class="form-control">
					{if isset($whopays_values) && $whopays_values && sizeof($whopays_values)}
						{foreach from=$whopays_values item='whopays_value'}
							<option value="{$whopays_value.value|escape:'htmlall':'UTF-8'}"{if isset($whopays) && $whopays == $whopays_value.value} selected{/if}>{$whopays_value.description|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					{/if}
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				<span title="{l s='This description will be displayed in the returns policy section of the listing page.' mod='ebay'}" class="label-tooltip" data-toggle="tooltip">
					{l s='Any other information' mod='ebay'}
				</span>
			</label>
			<div class="col-sm-9">
				<textarea name="ebay_returns_description" cols="120" rows="10" >{$ebayReturns|cleanHtml}</textarea>
			</div>
		</div>
	</fieldset>

	<fieldset style="margin-top:10px;">
		<div class="panel-heading"><span data-dialoghelp="http://sellerupdate.ebay.co.uk/autumn2013/picture-standards" data-inlinehelp="{l s='Select the size of your main photo and any photos you want to include in your description. Go to Preferences > images. Your images must comply with eBay’s photo standards.' mod='ebay'}">{l s='Photo options' mod='ebay'}</span></div>

		<div class="form-group">
      <label class="control-label col-sm-3">
				<span class="label-tooltip" title="{l s='This photo will appear as main photo in your listing.' mod='ebay'}">
					{l s='Default photo' mod='ebay'}
				</span>
      </label>
			<div class="col-sm-9">
				<select name="sizedefault" data-inlinehelp="{l s='This will be the main photo and will appear on the search result and item pages.' mod='ebay'}" class="form-control">
					{if isset($sizes) && $sizes && sizeof($sizes)}
						{foreach from=$sizes item='size'}
							<option value="{$size.id_image_type|escape:'htmlall':'UTF-8'}"{if $size.id_image_type == $sizedefault} selected{/if}
							data-width="{$size.width|escape:'htmlall':'UTF-8'}"
							data-height="{$size.height|escape:'htmlall':'UTF-8'}">
                {$size.name|escape:'htmlall':'UTF-8'} ({$size.width|escape:'htmlall':'UTF-8'}x{$size.height|escape:'htmlall':'UTF-8'}px)
							</option>
						{/foreach}
					{/if}
				</select>
			</div>
      <div class="col-sm-9 col-sm-push-3">
        <div class="help-block">
          {l s='If the image lenght is lower than 800*800px, the zoom option will be desactivated on eBay' mod='ebay'}
        </div>
      </div>
		</div>

		<div class="form-group">
      <label class="control-label col-sm-3">
				<span class="label-tooltip" title="{l s='This photo will appear as main photo in your listing’s description.' mod='ebay'}">
					{l s='Main photo' mod='ebay'}
				</span>
      </label>
			<div class="col-sm-9">
				<select name="sizebig" data-inlinehelp="{l s='This photo will appear as default photo in your listing\'s description.' mod='ebay'}" class="form-control">
					{if isset($sizes) && $sizes && sizeof($sizes)}
						{foreach from=$sizes item='size'}
							<option value="{$size.id_image_type|escape:'htmlall':'UTF-8'}"{if $size.id_image_type == $sizebig} selected{/if}
									data-width="{$size.width|escape:'htmlall':'UTF-8'}"
									data-height="{$size.height|escape:'htmlall':'UTF-8'}">
								{$size.name|escape:'htmlall':'UTF-8'} ({$size.width|escape:'htmlall':'UTF-8'}x{$size.height|escape:'htmlall':'UTF-8'}px)
							</option>
						{/foreach}
					{/if}
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				<span class="label-tooltip" title="{l s='This photo will appear as thumbnail in your listing\'s description.' mod='ebay'}">
					{l s='Small photo' mod='ebay'}
				</span>
			</label>
			<div class="col-sm-9">
				<select name="sizesmall" class="form-control">
					{if isset($sizes) && $sizes && sizeof($sizes)}
						{foreach from=$sizes item='size'}
							<option value="{$size.id_image_type|escape:'htmlall':'UTF-8'}"{if $size.id_image_type == $sizesmall} selected{/if}
									data-width="{$size.width|escape:'htmlall':'UTF-8'}"
									data-height="{$size.height|escape:'htmlall':'UTF-8'}">
                {$size.name|escape:'htmlall':'UTF-8'} ({$size.width|escape:'htmlall':'UTF-8'}x{$size.height|escape:'htmlall':'UTF-8'}px)
							</option>
						{/foreach}
					{/if}
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Number of additional pictures' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<input type="number" class="form-control width-num" name="picture_per_listing" value="{$picture_per_listing|escape:'htmlall':'UTF-8'}" onchange="checkInputParameters()">
			</div>
			<div class="col-sm-9 col-sm-push-3">
				<div class="help-block">
					{l s='0 will send one picture' mod='ebay'}
				</div>
			</div>
		</div>
			{*
		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Send new product images on the next synchronization' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<a id="reset-image" href="#" target="_blank" class="btn btn-default">
					<i class="icon-off"></i>
					{l s='Active' mod='ebay'}
				</a>
			</div>
		</div>

		<div id="reset-image-result" class="col-sm-9 col-sm-push-3"></div>
			*}
		<div class="form-group">
			<div class="col-sm-9 col-sm-push-3">
				<div class="checkbox">
					<label for="picture_skip_variations">
						<input type="checkbox" id="picture_skip_variations" name="picture_skip_variations" value="1" {if $picture_skip_variations} checked="checked"{/if}>
						{l s='Do not send variations images' mod='ebay'}
					</label>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
        <span title="{l s='Choose which variation’s details do you want to see in the default photo.' mod='ebay'}" class="label-tooltip" data-toggle="tooltip">
					{l s='Select attribute used for variation image' mod='ebay'}
				</span>
			</label>
			<div class="col-sm-9">
				<select name="picture_charact_variations" data-inlinehelp="{l s='This will be the main photo and will appear on the search result and item pages.' mod='ebay'}" class="form-control">
					{if isset($itemspecifics) && $itemspecifics && sizeof($itemspecifics)}
						{foreach from=$itemspecifics item='itemspecific'}
							<option value="{$itemspecific.id_attribute_group|escape:'htmlall':'UTF-8'}"{if $itemspecific.id_attribute_group == $picture_charact_variations} selected{/if}>{$itemspecific.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					{/if}
				</select>
			</div>
		</div>
	</fieldset>

	<!-- Listing Durations -->
	<fieldset style="margin-top:10px;">
		<div class="panel-heading">{l s='Listing Duration' mod='ebay'}</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Listing duration' mod='ebay'}
				<a class="kb-help" data-errorcode="{$help_gtc.error_code}" data-module="ebay" data-lang="{$help_gtc.lang}" module_version="{$help_gtc.module_version}" prestashop_version="{$help_gtc.ps_version}" href="" target="_blank">&nbsp;<i class="icon-info-circle"></i></a>
			</label>
			<div class="col-sm-9">
				<select name="listingdurations" class="form-control">
					{foreach from=$listingDurations item=listing key=key}
						<option value="{$key|escape:'htmlall':'UTF-8'}" {if $ebayListingDuration == $key}selected="selected" {/if}>{$listing|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				</select>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-9 col-sm-push-3">
				<div class="checkbox">
					<label for="automaticallyrelist">
						<input type="checkbox" id="automaticallyrelist" name="automaticallyrelist" {if isset($automaticallyRelist) && $automaticallyRelist} checked="checked" {/if} />
						{l s='Automatic relist' mod='ebay'}
					</label>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Out of Stock status ' mod='ebay'}
				<a class="kb-help" data-errorcode="{$help_out_of_stock.error_code}" data-module="ebay" data-lang="{$help_out_of_stock.lang}" module_version="{$help_out_of_stock.module_version}" prestashop_version="{$help_out_of_stock.ps_version}" href="" target="_blank">&nbsp;<i class="icon-info-circle"></i></a>
			</label>
			<div class="col-sm-9">
				<input type="text" readonly value="{if $out_of_stock_value}{l s='Activated' mod='ebay'}{else}{l s='Deactivated' mod='ebay'}{/if}" class="d-ib" style="text-align: center;">
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
                {l s='Limit of ebay stock' mod='ebay'}
				<a class="kb-help" data-errorcode="{$help_limit_of_stock.error_code}" data-module="ebay" data-lang="{$help_limit_of_stock.lang}" module_version="{$help_limit_of_stock.module_version}" prestashop_version="{$help_limit_of_stock.ps_version}" href="" target="_blank">&nbsp;<i class="icon-info-circle"></i></a>
			</label>
			<div class="col-sm-9">
				<input type="number" name="limitEbayStock" max="50" min="0" class="form-control width-num" id="limitEbayStock"
					   value="{if $limitEbayStock || $limitEbayStock === '0'}{$limitEbayStock}{else}50{/if}">
			</div>
			<div class="col-sm-9 col-sm-push-3">
				<div class="help-block">
					{l s='0 - unlimited, 1 min - 50 max to limit stock' mod='ebay'}
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-9 col-sm-push-3">
				<div class="checkbox">
					<label for="restrictSync">
						<input type="checkbox" id="restrictSync" name="restrictSync" value="1" {if isset($restrictSync) && $restrictSync} checked="checked" {/if} />
                        {l s='Synchronization only price and quantity' mod='ebay'}
					</label>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-9 col-sm-push-3">
				<div class="checkbox">
					<label for="syncNewProd">
						<input type="checkbox" id="syncNewProd" name="syncNewProd" value="1" {if isset($syncNewProd) && $syncNewProd} checked="checked" {/if} />
                        {l s='Synchronization only new product' mod='ebay'}
					</label>
				</div>
			</div>
		</div>
	</fieldset>

	<div class="panel-footer id="buttonEbayParameters" style="margin-top:5px;">
		<a href="#categoriesProgression" {if $catLoaded}id="displayFancybox"{/if}>
			<input class="primary button" name="submitSave" type="hidden" value="{l s='Save and continue' mod='ebay'}" />
			<button class="btn btn-default pull-right" type="submit" id="save_ebay_parameters_annonces">
				<i class="process-icon-save"></i>
				{l s='Save' mod='ebay'}
			</button>
		</a>
	</div>
	<div id="ebayreturnshide" style="display:none;">{$ebayReturns|escape:'htmlall':'UTF-8'}</div>
</form>

<script>
	$(document).ready(function(){
	    $('#limitEbayStock').change(changeLimitStock);
	    function changeLimitStock(){
            var stock = $('#limitEbayStock');
            if (stock.val() > 50){
                $('#limitEbayStock').val('50');
			}
			if (stock.val() < 0){
                $('#limitEbayStock').val('0');
			}
		}

		$('select[name = \'sizebig\'], select[name = \'sizedefault\']').change(checkSizeImage);

        checkSizeImage();

		function checkSizeImage(){
            var width;
            var height;
            var warning;

            $('select[name = \'sizebig\'], select[name = \'sizedefault\']').each(function(){
                width = $(this).find('option:selected').attr('data-width');
                height = $(this).find('option:selected').attr('data-height');
			});

		}

	});
</script>
