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

<form action="{$url}" method="post" class="form form-horizontal panel" id="advancedConfigForm">
	<fieldset>
		<div class="panel-heading">{l s='Logs' mod='ebay'}</div>
		
		<div class="form-group">
			<div class="col-sm-9 col-sm-push-3">
				<div class="checkbox">	
					<label for="api_logs">
						<input type="checkbox" id="api_logs" name="api_logs" value="1"{if $api_logs} checked="checked"{/if}>
						{l s='API Logs' mod='ebay'}
					</label>
				</div>
			</div> 
		</div>       
		
		{if !$is_writable && $activate_logs}<p class="warning">{l s='The log file is not writable' mod='ebay'}</p>{/if}

		<div class="form-group">
			<div class="col-sm-9 col-sm-push-3">
				<div class="checkbox">
					<label for="activate_logs">
						<input type="checkbox" id="activate_logs" name="activate_logs" value="1"{if $activate_logs} checked="checked"{/if}>
						{l s='Applicative Logs' mod='ebay'}
					</label>
				</div>
			</div>
		</div>        

		{if $log_file_exists}
			<div class="form-group">
				<div class="col-sm-9 col-sm-push-3">
					<a href="#" class ='logs btn btn-default' data-inlinehelp="{l s='When log file size reaches 100 mo, log file is emptied automatically.' mod='ebay'}">
						<i class="icon-download"></i>
						<span>
							{l s='Download' mod='ebay'}
						</span>
					</a>

					<a href="#" class ='clear-logs btn btn-default' ">
						<i class="icon-trash"></i>
						<span>
							{l s='Clear logs' mod='ebay'}
						</span>
					</a>
					<p id="clear-logs-message" style="display: inline-block; margin-left: 10px"></p>
				</div>
			</div>
		{/if}

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Logs Conservation Duration' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<input type="text" name="logs_conservation_duration" class="width-num" value="{$logs_conservation_duration|escape:'htmlall':'UTF-8'}">
			</div>
		</div>
	</fieldset>
	  
	<fieldset style="margin-top: 20px;">
	   
		<div class="panel-heading">{l s='Check Database' mod='ebay'}</div>

		<div class="form-group">
			<div class="col-sm-9 col-sm-push-3">
				<a id="check_database" href="#" target="_blank" class="btn btn-default">
					{l s='Start checking' mod='ebay'}
				</a>
				<div class="help-block">
					{l s='Click on "Start checking" if you want to proceed to verify your eBay database' mod='ebay'}
				</div>
			</div>
		</div>

		<div id="check_database_progress" style="display: none;">
			<div class="progress">
			  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="">
			  </div>
			</div>	
		</div>
		<div id="check_database_logs" style="display:none;">
			<table class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%;">
				<thead>
					<tr class="nodrag nodrop">
						<th style="width:10%">
							{l s='Status' mod='ebay'}
						</th>
						<th style="width:45%">
							{l s='Description' mod='ebay'}
						</th>
						<th style="width:45%">
							{l s='Result' mod='ebay'}
						</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>

	</fieldset>

	<fieldset style="margin-top: 20px;">

		<div class="panel-heading">{l s='Category definition & upgrade' mod='ebay'}</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Category definition comparison tool' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<a name="comparison"  id="check_categories" href="#" target="_blank" class="btn btn-default" data-inlinehelp="{l s='Compare your category definitions with last available category definitions from eBay' mod='ebay'}">{l s='Start comparison' mod='ebay'}</a>
			</div>
		</div>

		<div id="check_categories_logs" style="display: none;">
			<span> {l s='eBay categories used in your eBay module configuration compared to last available category definitions from eBay' mod='ebay'}</span></br>
			</br><table class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%;">
				<thead>
				<tr class="nodrag nodrop">
					<th style="width:70%">
						{l s='Ebay Category Configured in PrestaShop' mod='ebay'}
					</th>

					<th style="width:30%">
						{l s='Statut' mod='ebay'}
					</th>
				</tr>
				</thead>
				<tbody id="table_resynch">
				</tbody>
			</table>
		</div>

		<div id="new_cat" style="display: none;    margin-top: 12px;max-height: 300px; overflow-y: scroll;">
			<span> {l s='Some categories are existing in the new definition of categories from eBay but not in your definition. You might want to reload your categories' mod='ebay'}</span></br>
			<ul id="categories_new">

			</ul>

		</div>

		<div id="div_resynch" style="display: none; text-align: center; font-family: sans-serif; font-size: 14px;">
			<span>{l s='Have you read ' mod='ebay'}<a class="kb-help" style ="width: auto;height: 20px;background-image: none;" data-errorcode="{$help_Cat_upd.error_code}" data-module="ebay" data-lang="{$help_Cat_upd.lang}" module_version="{$help_Cat_upd.module_version}" prestashop_version="{$help_Cat_upd.ps_version}" href="" target="_blank">{l s='this article' mod='ebay'}&nbsp;<i class="icon-info-circle"></i></a>{l s=' about category definition & reloading?' mod='ebay'}</span>
			</br>
			</br>
			<div class="checkbox">
				<label for="accepted" class="text-left">
					<input type="checkbox" name="accepted" id="accepted" value="yes">
					<span style="color: red;"> {l s='I have understood all my categories will need to reconfigured manually' mod='ebay'}</span>
				</label>
			</div>
			</br><a class='btn btn-warning disable_link' id="ReCategoriespar" href="{$smarty.server.REQUEST_URI}&resynchCategories='1'">{l s='Reaload category definition' mod='ebay'}</a>
		</div>

		<div style="margin-top: 30px">
			<div class="form-group">
				<label class="control-label col-sm-3">
					{l s='Category definition upgrade tool' mod='ebay'}
				</label>
				<div class="col-sm-9">
					<a name="resynch" id="ResynchCategories" class="btn btn-warning" href ="#div_resynch" data-inlinehelp="{l s='Upgrade category definition with last definition from eBay. You will need to reconfigure all your categories.' mod='ebay'}">{l s='Start upgrade' mod='ebay'}</a>
				</div>
			</div>
		</div>


		<div style="margin-top: 30px">
			<div class="form-group">
				<label class="control-label col-sm-3">
					{l s='Reload store eBay categories tool' mod='ebay'}
				</label>
				<div class="col-sm-9">
					<a name="reload_categories_store" id="reload_categories_store" class="btn btn-warning" href ="#">{l s='Reload store categories' mod='ebay'}</a>
				</div>
			</div>
		</div>

	</fieldset>

   <fieldset style="margin-top: 20px; display:none;">
	   
		<div class="panel-heading">{l s='eBay module Data Usage' mod='ebay'}</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Statistics' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<div class="radio">
					<label for="stats">
						<input type="radio" id="stats" name="stats" value="0" > {l s='No thanks' mod='ebay'}
					</label>
				</div>
				<div class="radio">
					<label for="stats_y">
						<input type="radio" id="stats_y" name="stats" value="1" checked="checked"> {l s='I agree' mod='ebay'}
					</label>
				</div>

				<div class="help-block">
					{l s='Help us improve the eBay Module by sending anonymous usage stats' mod='ebay'}
				</div>
			</div>
		</div>
		
	</fieldset>
	
	<div id="buttonEbayParameters" class="panel-footer">
		<a href="#categoriesProgression">
			<input class="primary button" name="submitSave" type="hidden" value="{l s='Save and continue' mod='ebay'}" />
			<input class="primary button" type="hidden" id="save_ebay_advanced_parameters" value="{l s='Save and continue' mod='ebay'}" />
			<button class="btn btn-default pull-right" type="submit" >
				<i class="process-icon-save"></i>
				{l s='Save' mod='ebay'}
			</button>
		</a>
	</div>
</form>
<form id="reload_categories_store_go" method="post"
	  action="{$url}">
	<input type="hidden" name="refresh_store_cat" value="1"/>
	<input type="hidden" class="btn btn-warning" type="submit" value="{l s='Reload store categories' mod='ebay'}"/>
</form>
	
{literal}
	<script>
		var formAdvancedParametersController = "{/literal}{$formAdvancedParametersController|addslashes}{literal}";
		var token = "{/literal}{$ebay_token}{literal}";
		$(document).ready(function() {
			setTimeout(function() {					
				$('#ebay_returns_description').val($('#ebayreturnshide').html());
			}, 1000);
		});


		$('.logs').click(function(e) {
			e.preventDefault();
            window.open(formAdvancedParametersController + '&action=getLogs');
		});

        $('.clear-logs').click(function(e) {
            e.preventDefault();
			if($('#activate_logs').is(':checked')){
                $.ajax({
					type: 'POST',
					url: formAdvancedParametersController,
					data: {
						ajax: true,
						action: 'ClearLogs'
					},
					beforeSend: function() {
						$('#clear-logs-message').show().text(clearing).css({color : 'yellow'});
					},
					success: function(response){
						if(response)
							$('#clear-logs-message').text(cleared).css({color : 'green'});
						else
						    $('#clear-logs-message').text(notActivated).css({color : 'red'});
						setTimeout(function () {
                            $('#clear-logs-message').fadeOut('slow');
                        }, 5000);
					}
            });
			}
			else {
                $('#clear-logs-message').text(notActivated).css({color : 'red'});
            }
        });

		$('#reload_categories_store').click(function(e) {
			e.preventDefault();
			$('#reload_categories_store_go').submit();
		});
		
		$('#token-btn').click(function() {
				window.open(module_dir + 'ebay/pages/getSession.php?token={/literal}{$ebay_token}{literal}');
		});
		
		$('.sync_products_mode').change(function() {
			if ($(this).val() == 'cron') {
				$('#sync_products_by_cron_url').show();
			} else {
				$('#sync_products_by_cron_url').hide();
			}
		});

		$('.sync_orders_mode').change(function() {
			if ($(this).val() == 'cron') {
				$('#sync_orders_by_cron_url').show();
			} else {
				$('#sync_orders_by_cron_url').hide();
			}
		});

		$('.sync_orders_returns_mode').change(function() {
			if ($(this).val() == 'cron') {
				$('#sync_orders_returns_by_cron_url').show();
			} else {
				$('#sync_orders_returns_by_cron_url').hide();
			}
		});


			$(function() {
				$('#check_database').click(function(e){
					e.preventDefault();
					// Premier tour : Récuperer le nombre de table
					// Foreach de toutes les tables
					$.ajax({
						type: 'POST',
						url: formAdvancedParametersController,
						data: {
						    ajax: true,
							action: 'CheckDatabase',
							actionProcess: 'getNbTable',
                        },
						beforeSend: function() {
							$('#check_database_logs tbody tr').remove();
						    // $('#reset-image-result').css('color', 'orange').text("{/literal}{l s='Activation in progress...' mod='ebay'}{literal}");
						},
						success: function( data ){
							$('#check_database_progress').attr('data-nb_database', data);
							$('#check_database_progress').show();
							$('#check_database_logs').show();
							launchDatabaseChecking(1);
						}
					});
				});
			});

			$(function() {
				$('#check_categories').click(function(e){
					e.preventDefault();
					$('#check_categories_logs').show();
					$('#table_resynch tr').remove();
					$('#table_resynch').append("<tr style='font-weight: bold;'><td colspan='2' ><img src='{/literal}{$_module_dir_|escape:'htmlall':'UTF-8'}{literal}ebay/views/img/loading-small.gif' alt=''/></td></tr>");

					comparation(1,false,false,0);
				});
			});

			function comparation(step,id_categories, nextDatas,encour,size){

				$.ajax({
					dataType: 'json',
					type: 'POST',
					url: formAdvancedParametersController,
					data: {
					    ajax: true,
						action: 'checkCategory',
						actionProcess: 'checkCategories',
						id_profile_ebay: id_ebay_profile,
						step: step,
						id_categories: id_categories,
                    },
					beforeSend: function() {

					},
					success: function( data ) {

						if (step == 1 ||step == 2) {

							if(nextDatas == false){
								nextDatas = data;
							}

							if(step == 1) {
								size = nextDatas.length;
								size= size - 1;
							}

							categoryId = nextDatas[0].CategoryID;
							nextDatas.shift();

							if (nextDatas.length == 0) {
									comparation(3,false,false,false,false);
							} else{
								if(step == 2) {
									$('#table_resynch tr').last().remove();
									encour = encour +1;

									$('#table_resynch').append("<tr class='version_ok'><td>" + encour + "/"+ size +"</td><td style='color: #72C279;'></td></tr>");
								}
								comparation(2, categoryId, nextDatas,encour,size);
							}

						}
						if (step == 3) {
							$('#table_resynch tr').remove();
							if (data['table'] != null) {
								$.each(data['table'], function (key, value) {
									if (value != 1) {
										$('#table_resynch').append("<tr class='fail'><td>" + key + "</td><td style='color: red;'>" + category_false + "</td></tr>");
									} else {
										$('#table_resynch').append("<tr class='version_ok'><td>" + key + "</td><td style='color: #72C279;'>" + category_true + "</td></tr>");
									}
								});

								$('#check_category_progress').show();
								$('#check_category_logs').show();

								if ($('.fail').length) {
									$('#table_resynch').append("<tr style='background-color: red;font-weight: bold;'><td colspan='2' >" + categories_false + "</td></tr>");
								} else {
									$('#table_resynch').append("<tr style='background-color: #DFF2BF;font-weight: bold;'><td colspan='2' >" + categories_true + "</td></tr>");

								}
							} else {
								$('#table_resynch').append("<tr style='background-color: red;font-weight: bold;'><td colspan='2' >" + categories_null + "</td></tr>");
							}
							if (data['new'] != false) {
								$.each(data['new'], function (key, value) {

								$('ul#categories_new').append("<li>" + value['name'] + "</li>");
							})
							$('#new_cat').show();
						}
					}
				}
			});
		}

		$('#ResynchCategories').fancybox({
			'parent': '#popin-container'
		});

		$('#accepted').change(function() {
			var val = $(this).prop('checked');

			if (val) {
				$('#ReCategoriespar').removeClass('disable_link');
			} else {
				$('#ReCategoriespar').addClass('disable_link');
			}
		});

		{/literal}
	</script>

<script type="text/javascript" src="{$_module_dir_|escape:'htmlall':'UTF-8'}ebay/views/js/advancedParameters.js"></script>

<script type="text/javascript">
	var category_true = "{l s='Category definition is already last version' mod='ebay'}";
	var category_false = "{l s='Category definition is not in last version, you may need to reload categories' mod='ebay'}";
	var categories_true = "{l s='All configured categories are already using last category definition version, no action to be taken.' mod='ebay'}";
	var categories_false = "{l s='The following categories are not configured based on the last category definition, you may need to reload categories.' mod='ebay'}";
	var categories_null = "{l s='No category to compare because you did not set up any category in tab Settings > Categories' mod='ebay'}";
	var clearing = "{l s='Clearing' mod='ebay'}";
	var cleared = "{l s='Job done! Log\'s file is empty!' mod='ebay'}";
	var notActivated = "{l s='To clear logs you need to activate them first!' mod='ebay'}";
</script>

<style>
	.disable_link {
		pointer-events: none;
		cursor: default;
		opacity: 0.4;
	}
	.link_resynch {
		border: 1px solid;
		border-radius: 4px;
		width: 20%;
		text-decoration: none;
		margin: auto;
		color: white;
		padding: 5px;
		background-color: rgb(23, 119, 182);
	}
</style>
