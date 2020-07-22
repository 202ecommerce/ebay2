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
* @author 202-ecommerce <tech@202-ecommerce.com>
* @copyright Copyright (c) 2017-2020 202-ecommerce
* @license Commercial license
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($relogin) && $relogin}
	{literal}
	<script>
		$(document).ready(function() {
			var win = window.location = '{/literal}{$redirect_url}{literal}';
		});		

	</script>
	{/literal}
{else}
<script type="text/javascript">
	var id_shop = '{$id_shop|escape:'htmlall':'UTF-8'}';
	var catLoaded = 0;
	{if $catLoaded}
		catLoaded = 1;
	{/if}
	var tooltip_numeric = "{l s='You must enter a number.' mod='ebay'}";
	var tooltip_max_pictures = "{l s='The maximum number is 12, so you can put up to 11 in this field.' mod='ebay'}";
	var regenerate_token_show = false;
	{if $regenerate_token != false}
	regenerate_token_show = true;
	{/if}
	var categories_ebay_l = {ldelim}
		'thank you for waiting': "{l s='Thank you for waiting while creating suggestions' mod='ebay'}",
		'no category selected' : "{l s='No category selected' mod='ebay'}",
		'No category found'		 : "{l s='No category found' mod='ebay'}",
		'You are not logged in': "{l s='You are not logged in' mod='ebay'}",
		'Settings updated'		 : "{l s='Settings updated' mod='ebay'}",
		'Unselect products'		: "{l s='Unselect products that you do NOT want to list on eBay' mod='ebay'}",
		'Unselect products clicked' : "{l s='Unselect products that you do NOT want to list on eBay' mod='ebay'}",
		'Products' : "{l s='Products' mod='ebay'}",
		'Stock' : "{l s='Stock' mod='ebay'}",
		'Finish' : "{l s='Finish' mod='ebay'}",
		'An error has occurred' : "{l s='An error has occurred' mod='ebay'}",
		'Waiting' : "{l s='Waiting' mod='ebay'}",
		'categories loaded success' : "{l s='categories loaded successfully.' mod='ebay'}",
		'Download subcategories of' : "{l s='Download subcategories of' mod='ebay'}",
		{rdelim};
	// Import Category From eBay
	function loadCategoriesFromEbay(step, id_category, row) {
		var admin_path = "{$admin_path}";
		alertOnExit(true, alert_exit_import_categories);
		step = typeof step !== 'undefined' ? step : 1;
		id_category = typeof id_category !== 'undefined' ? id_category : false;
		row = typeof row !== 'undefined' ? row : 2;

		$.ajax({
			type: "POST",
			dataType: 'json',
			url: formController,
			data: {
			    ajax: true,
				action: 'LoadCategoriesFromEbay',
				profile: id_ebay_profile,
				step: step,
				id_category: id_category,
				admin_path: admin_path,
            },
			success: function (data) {
				if (data == "error") {
					if (step == 1) {
						$('#cat_parent').addClass('error');
						$('#cat_parent td:nth-child(3)').text(categories_ebay_l['An error has occurred']);
						$('#exit_load_cat').show();
					}
					else if (step == 2) {
						$('#load_cat_ebay tbody tr:nth-child(' + row + ')').addClass('error');
						$('#exit_load_cat').show();
					}
					alertOnExit(false, "");
				}
				else {
					var output;

					if (step == 1) {
						for (var i in data) {
							output += '<tr class="standby" data-id="' + data[i].CategoryID + '"><td></td><td>' + categories_ebay_l['Download subcategories of'] + ' ' + data[i].CategoryName + '</td><td>' + categories_ebay_l['Waiting'] + '</td></tr>';
						}
						console.log(data);
						var count = $.map(data, function (n, i) {
							return i;
						}).length;
						$('#cat_parent').removeClass('load').addClass('success');
						$('#cat_parent td:nth-child(3)').text(categories_ebay_l['Finish'] + ' - ' + count + ' ' + categories_ebay_l['categories loaded success']);
						$('#load_cat_ebay tbody').append(output);

						$('#load_cat_ebay tbody tr:nth-child(2)').addClass('load');
						loadCategoriesFromEbay(2, $('#load_cat_ebay tbody tr:nth-child(2)').attr('data-id'), 2);
					}
					else if (step == 2) {
						var count = $.map(data, function (n, i) {
							return i;
						}).length;
						$('#load_cat_ebay tbody tr:nth-child(' + row + ')').removeClass('load').addClass('success');

						$('#load_cat_ebay tbody tr:nth-child(' + row + ') td:nth-child(3)').text(categories_ebay_l['Finish'] + ' - ' + count + ' ' + categories_ebay_l['categories loaded success']);

						var next = row + 1;
						if ($('#load_cat_ebay tbody tr:nth-child(' + next + ')').length > 0) {
							$('#load_cat_ebay tbody tr:nth-child(' + next + ')').addClass('load');
							loadCategoriesFromEbay(2, $('#load_cat_ebay tbody tr:nth-child(' + next + ')').attr('data-id'), next);
						}
						else {
							loadCategoriesFromEbay(3);
							$('#load_cat_ebay').css('display', 'none');
							$('.hidden.importCatEbay').removeClass('hidden').removeClass('importCatEbay');
							$('.warning.big.tips.h').show();
							alertOnExit(false, "");
							$('#menuTab2').removeClass('succes');
							$('#menuTab2').addClass('wrong');
							$('#menuTab8').removeClass('succes');
							$('#menuTab8').addClass('wrong');
							$.fancybox.close();
							var location_array = document.location.search.split('&');
							var url = new Array();
							location_array.forEach(function(currentValue){
                                if (currentValue.indexOf('resynchCategories') == -1){
									url.push(currentValue);
								}
							});
							document.location.search = url.join('&') ;
							//return loadCategories();

						}
						alertOnExit(false, "");
					}

				}
			}
		});

	}
	$(document).ready(function(){
		if(regenerate_token_show) {
			$('.regenerate_token_button').show();
			$('.regenerate_token_button label').css('color', 'red').html("{l s='You must regenerate your authentication token' mod='ebay'}");
			$('.regenerate_token_click').hide();
		}
		$('.regenerate_token_click span').click(function(){
			$('.regenerate_token_button').show();
			$('.regenerate_token_click').hide();
		});
		$('.load_cat_sync').fancybox({
			'modal': true,
			'showCloseButton': false,
			'padding': 0,
			'parent': '#popin_load_category-container',
		});
		$('#exit_load_cat').click(function(){
			$.fancybox.close();
		});

		if(catLoaded == 1){

			$('.load_cat_sync').click();
			loadCategoriesFromEbay();
		}

		$('.open-btn').click(function () {
			var $input = $(this).parents('.input-group').first().find('input');
            window.open($input.val());
        });
        $('.copy-btn').click(function () {
            var $input = $(this).parents('.input-group').first().find('input');
            $($input).select();
            document.execCommand('copy');
        });


	});



</script>

	{if isset($check_token_tpl)}
	<fieldset id="regenerate_token">
		<legend>{l s='Token' mod='ebay'}</legend>
			{$check_token_tpl|ebayHtml}
	</fieldset>	
	{/if}
		
	<form action="{$url|escape:'htmlall':'UTF-8'}" method="post" class="form form-horizontal panel">
		<fieldset>
			<div class="panel-heading">{l s='Account details' mod='ebay'}</div>

			{if !$ebay_paypal_email}
				<div class="alert alert-info alert-no-icon">
                    {l s='To list your products on eBay, you need to create' mod='ebay'} <a href="https://www.paypal.com/" target="_blank">{l s='a PayPal account.' mod='ebay'}</a>.
				</div>
			{/if}

			
			<input type="hidden" name="ebay_shop" value="{$ebayShopValue|escape:'htmlall':'UTF-8'}" />            
			
			<div class="form-group">
				<label class="control-label col-sm-3">
					{l s='Paypal email address' mod='ebay'}
				</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" size="20" name="ebay_paypal_email" value="{$ebay_paypal_email|escape:'htmlall':'UTF-8'}"/>
				</div>

				<div class="col-sm-9 col-sm-push-3">
					<div class="help-block">
						{l s='You have to set your PayPal e-mail account, this is the only payment solution available with this module.' mod='ebay'}<a class="kb-help" data-errorcode="{$help.code_payment_solution|escape:'htmlall':'UTF-8'}" data-module="ebay" data-lang="{$help.lang|escape:'htmlall':'UTF-8'}" module_version="{$help.module_version|escape:'htmlall':'UTF-8'}" prestashop_version="{$help.ps_version|escape:'htmlall':'UTF-8'}" href="" target="_blank">&nbsp;<i class="icon-info-circle"></i></a>
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-sm-3">
					<span class="label-tooltip" title="{l s='This currency will be used for your products sold on eBay' mod='ebay'}">{l s='Currency' mod='ebay'}</span>
				</label>
				<div class="col-sm-9">
					<select name="currency" class="form-control">
						{if isset($currencies) && $currencies && sizeof($currencies)}
							{foreach from=$currencies item='currency'}
								<option value="{$currency.id_currency|escape:'htmlall':'UTF-8'}"{if $currency.id_currency == $current_currency} selected{/if}>{$currency.name|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						{/if}
					</select>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-sm-3">
          <span class="label-tooltip" title="{l s='Here you have to add your shop\'s postal code' mod='ebay'}">{l s='Item location' mod='ebay'}</span>
				</label>
				<div class="col-sm-9">
					<input type="text" size="20" name="ebay_shop_postalcode" value="{$shopPostalCode|escape:'htmlall':'UTF-8'}" class="form-control" placeholder="{l s='Your shop\'s postal code' mod='ebay'}" />
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3">
          <span class="label-tooltip" title="{l s='Here you have to add your shop\'s country' mod='ebay'}">{l s='Item Country' mod='ebay'}</span>
				</label>
				<div class="col-sm-9">
					<select name="ebay_shop_country" class="form-control">
						<option value="" disabled selected>{l s='Your shop\'s country' mod='ebay'}</option>
						{foreach from=$ebay_shop_countries  key=k item=ebay_shop_country}
							<option value="{$k|escape:'htmlall':'UTF-8'}" {if $current_ebay_shop_country == $k} selected="selected"{/if}>{$ebay_shop_country|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-9 col-sm-push-3">
					<div class="checkbox">
						<label for="immediate_payment">
							<input type="checkbox" id="immediate_payment" name="immediate_payment" value="1"{if $immediate_payment} checked="checked"{/if}>
							{l s='Immediate Payment' mod='ebay'}
						</label>
					</div>
				</div>
			</div>

			<div class="col-sm-9 col-sm-push-3 pos-r">
				<span class="regenerate_token_click btn btn-default label-tooltip" data-toggle="tooltip" title="{l s='Use only if you get a message saying that your authentication is expired.' mod='ebay'}">
					<i class="icon-refresh"></i>
					<span>{l s='Generate a new authentication token' mod='ebay'}</span>
					{* {l s='Use only if you get a message saying that your authentication is expired.' mod='ebay'} *}
				</span>
				<span class="tooltip" data-toggle="tooltip" title="{l s='Use only if you get a message saying that your authentication is expired.' mod='ebay'}">
					<i class="process-icon-help"></i>
				</span>
			</div>

			<div class="regenerate_token_button col-sm-9 col-sm-push-3 pos-r" style="display: none;">
				<label>{l s='Regenerate Token' mod='ebay'} :</label>
				<a href="{$url|escape:'htmlall':'UTF-8'}&action=regenerate_token">
					<input type="button" id="token-btn" class="button" value="{l s='Regenerate Token' mod='ebay'}" />
				</a>
			</div>

		</fieldset>

		<fieldset style="margin-top:10px;">
			<div class="panel-heading">{l s='Sync' mod='ebay'}</div>

			<div class="form-group">
				<label class="control-label col-sm-3">
					{l s='Sync Orders' mod='ebay'}
				</label>
				<div class="col-sm-9">
					<div class="radio">
						<label for="sync_orders_mode">
							<input type="radio" size="20" id="sync_orders_mode" name="sync_orders_mode" class="sync_orders_mode" value="save" {if $sync_orders_by_cron == false}checked="checked"{/if}/>
							{l s='every 30 minutes on page load' mod='ebay'}
						</label>
					</div>

					<div class="radio">
						<label for="sync_orders_mode_cron">
							<input type="radio" size="20" id="sync_orders_mode_cron" name="sync_orders_mode" class="sync_orders_mode" value="cron" {if $sync_orders_by_cron == true}checked="checked"{/if}/> 
							{l s='by CRON task' mod='ebay'}
						</label>
					</div>

					<div class="help-block">
						<div class="input-group" id="sync_orders_by_cron_url" style="{if $sync_orders_by_cron == false};display:none{/if}">
							<input type="text" class="form-control" value="{$sync_orders_by_cron_url}" readonly >
							<div class="input-group-btn">
								<button class="btn btn-default copy-btn" type="button">
									<i class="fa fa-copy"></i>
									<span>{l s='Copy' mod='ebay'}</span>
								</button>
								<button class="btn btn-default open-btn" type="button">
                                    {l s='Open' mod='ebay'}
								</button>
							</div>
						</div>
						{*<a id="sync_orders_by_cron_url" href="{$sync_orders_by_cron_url}" target="_blank" style="{if $sync_orders_by_cron == false};display:none{/if}">{$sync_orders_by_cron_path}</a>*}
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3">
					{l s='Sync Products' mod='ebay'}
				</label>
				<div class="col-sm-9">
					<div class="radio">
						<label for="sync_products_mode">
							<input type="radio" size="20" id="sync_products_mode" name="sync_products_mode" class="sync_products_mode" value="save" {if $sync_products_by_cron == false}checked="checked"{/if}/>
							{l s='on save' mod='ebay'}
						</label>
					</div>

					<div class="radio">
						<label for="sync_products_mode_cron">
							<input type="radio" size="20" id="sync_products_mode_cron" name="sync_products_mode" class="sync_products_mode" value="cron" {if $sync_products_by_cron == true}checked="checked"{/if}/>
							{l s='by CRON task' mod='ebay'}
						</label>
					</div>
					
					<div class="help-block">
						<div class="input-group" id="sync_products_by_cron_url" style="{if $sync_products_by_cron == false};display:none{/if}">
							<input type="text" class="form-control" value="{$sync_products_by_cron_url}" readonly >
							<div class="input-group-btn">
								<button class="btn btn-default copy-btn" type="button">
									<i class="fa fa-copy"></i>
									<span>{l s='Copy' mod='ebay'}</span>
								</button>
								<button class="btn btn-default open-btn" type="button">
                                    {l s='Open' mod='ebay'}
								</button>
							</div>
						</div>
						{*<a id="sync_products_by_cron_url" href="{$sync_products_by_cron_url}" target="_blank" style="{if $sync_products_by_cron == false};display:none{/if}">{$sync_products_by_cron_path}</a>*}
					</div>
				</div>
			</div>
	{if isset($sync_offers_by_cron_url)}
				<div class="form-group">
					<label class="control-label col-sm-3">
						{l s='Sync Offers' mod='ebay'}
					</label>
					<div class="col-sm-9">
						<div class="radio">
							<label for="sync_offers_mode_cron">
								<input type="radio" size="20" id="sync_offers_mode_cron" name="sync_offers_mode" class="sync_offers_mode" value="cron" checked="checked"/>
								{l s='by CRON task' mod='ebay'}
							</label>
						</div>

						<div class="help-block">
							<a id="sync_products_by_cron_url" href="{$sync_offers_by_cron_url}" target="_blank" style="{if $sync_products_by_cron == false};display:none{/if}">{$sync_offers_by_cron_url}</a>
						</div>
					</div>
				</div>
			{/if}

			{if $help_Cat_upd.ps_version > '1.4.11'}
				<div class="form-group">
					<label class="control-label col-sm-3">
						{l s='Synch cancellations, refunds and returns' mod='ebay'}
					</label>
					<div class="col-sm-9">
						<div class="radio">
							<label for="sync_orders_returns_mode">
								<input type="radio" size="20" id="sync_orders_returns_mode" name="sync_orders_returns_mode" class="sync_orders_returns_mode" value="save" {if $sync_orders_returns_by_cron == false}checked="checked"{/if}/>
								{l s='every 30 minutes on page load' mod='ebay'}
							</label>
						</div>

						<div class="radio">
							<label for="sync_orders_returns_mode_cron">
								<input type="radio" size="20" id="sync_orders_returns_mode_cron" name="sync_orders_returns_mode" class="sync_orders_returns_mode" value="cron" {if $sync_orders_returns_by_cron == true}checked="checked"{/if}/>
								{l s='by CRON task' mod='ebay'}
							</label>
						</div>

						<div class="help-block">
							<div class="input-group" id="sync_orders_returns_by_cron_url" style="{if $sync_orders_returns_by_cron == false};display:none{/if}">
								<input type="text" class="form-control" value="{$sync_orders_returns_by_cron_url}" readonly >
								<div class="input-group-btn">
									<button class="btn btn-default copy-btn" type="button">
										<i class="fa fa-copy"></i>
										<span>{l s='Copy' mod='ebay'}</span>
									</button>
									<button class="btn btn-default open-btn" type="button">
										{l s='Open' mod='ebay'}
									</button>
								</div>
							</div>
							{*<a id="sync_orders_returns_by_cron_url" href="{$sync_orders_returns_by_cron_url}" target="_blank" style="{if $sync_orders_returns_by_cron == false}display:none{/if}">{$sync_orders_returns_by_cron_path}</a>*}
						</div>
					</div>
				</div>
			{/if}

			<div class="form-group">
				<div class="col-sm-9 col-sm-push-3">
					<div class="checkbox">
						<label for="activate_resynchBP" class="control-label">
							<input type="checkbox" id="activate_resynchBP" name="activate_resynchBP" value="1"{if $activate_resynchBP == 1} checked="checked"{/if}>
							<span class="label-tooltip" title="{l s='If activiated, Business Policies created by PrestaShop will be overriden at every product synchronisation.' mod='ebay'}" data-toggle="tooltip">
								{l s='Always override Shipping Policies' mod='ebay'}
							</span>
						</label>
					</div>
				</div>
			</div>

			<div class="clear both"></div>
		</fieldset>

		<div class="panel-footer" id="buttonEbayParameters">
			<a href="#categoriesEbayProgression" class="load_cat_sync" style="display: none"></a>
			<input class="primary button" name="submitSave" type="hidden" value="{l s='Save and continue' mod='ebay'}" />
			<button class="btn btn-default pull-right" type="submit" id="save_ebay_parameters">
				<i class="process-icon-save"></i>
				{l s='Save' mod='ebay'}
			</button>
		</div>
	</form>
{/if}