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
<form action="{$formUrl|escape:'htmlall':'UTF-8'}" method="post" class="form form-horizontal panel">
	<fieldset>
		<div class="panel-heading">{l s='Dispatch time' mod='ebay'}</div>
		
		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Dispatch Time' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<select name="deliveryTime" id="deliveryTime" data-dialoghelp="#dispatchTime" data-inlinehelp="{l s='Specify a dispatch time of between 1-3 days.' mod='ebay'}" class="form-control">
					{foreach from=$deliveryTimeOptions item=deliveryTimeOption}
						<option value="{$deliveryTimeOption.DispatchTimeMax|escape:'htmlall':'UTF-8'}" {if $deliveryTimeOption.DispatchTimeMax == $deliveryTime} selected="selected"{/if}>{$deliveryTimeOption.description|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				</select>
			</div>
		</div>
	</fieldset>

	<script type="text/javascript">
		// <![CDATA[
		function addShipping(currentName, idPSCarrier, idEbayCarrier, additionalFee, nbSelect, id_zone, zone) {
			var lastId = 0;
			var current = $("#"+currentName);
			var hasValues = (typeof(idPSCarrier) != "undefined" && typeof(idEbayCarrier) != "undefined" && typeof(additionalFee) != "undefined");
            var selected_services = new Array();
            $('#domesticShipping .linked a').each(function(){
                selected_services.push($(this).attr('data-id'));
            });
			if (current.children('table').length > 0)
				lastId = parseInt(current.children('table').last().attr('data-nb')) + 1;
			var createSelecstShipping = '';
			createSelecstShipping += "<tr>";

			// Carrier Prestashop
			createSelecstShipping += "<td><label>{l s='Prestashop carrier' mod='ebay'}</label><div><select name='"+ (currentName == 'domesticShipping' ? 'psCarrier' : 'psCarrier_international') +"["+ lastId +"]' class='prestaCarrier'><option value='' selected>{l s='Select a PrestaShop carrier' mod='ebay'}</option>";
			{foreach from=$newPrestashopZone item=zone}
				{if !empty($zone.carriers)}
				 	createSelecstShipping += "<optgroup label='{$zone.name|escape:'htmlall':'UTF-8'}'>";
						{foreach from=$zone.carriers item=carrier}
							var testPSCarrier = (typeof(idPSCarrier) != "undefined"  && idPSCarrier == {$carrier.id_carrier|escape:'htmlall':'UTF-8'} && id_zone == {$zone.id_zone|escape:'htmlall':'UTF-8'});
							createSelecstShipping += "<option  "+ (testPSCarrier ? 'selected="selected"' : '')  +" data-realname='{$zone.name|escape:'htmlall':'UTF-8'}, {$carrier.name|escape:'htmlall':'UTF-8'}' value='{$carrier.id_carrier|escape:'htmlall':'UTF-8'}-{$zone.id_zone|escape:'htmlall':'UTF-8'}' data-maxprice='{$carrier.price|escape:'htmlall':'UTF-8'}'>{$carrier.name|escape:'htmlall':'UTF-8'}</option>";
							if (testPSCarrier) {
								var valuePSCarrier = " {$carrier.name|escape:'htmlall':'UTF-8'} ";
							}
						{/foreach}
					createSelecstShipping += "</optgroup>";
				{/if}
			{/foreach}
			createSelecstShipping += "</select><div class='help-block'>{l s='Start by choosing a PrestaShop carrier to replicate on eBay' mod='ebay'}</div></div>";

			createSelecstShipping += "</td>";

			// end carrier Prestashop

			// Carrier eBay
			createSelecstShipping += "<td class='linked "+ (currentName == 'domesticShipping' ? '' : '') +"'' style='visibility:hidden'><label data-validate='{l s='Linked to eBay' mod='ebay'}'>{l s='Linked' mod='ebay'}"+(hasValues ? valuePSCarrier : '')+"{l s='with an eBay carrier' mod='ebay'}</label><div><select name='"+ (currentName == 'domesticShipping' ? 'ebayCarrier' : '') +"[" + lastId + "]' class='eBayCarrier'><option value=''>{l s='Select eBay carrier' mod='ebay'}</option>";
			{foreach from=$eBayCarrier item=carrier}
			{if isset($carrier.id_shipping_service)}

            if (('{$carrier.InternationalService|escape:'htmlall':'UTF-8'}' !== 'true' && currentName == 'domesticShipping')
                && selected_services.indexOf('{$carrier.id_shipping_service}') == -1)
                createSelecstShipping += "<option data-id='{$carrier.id_shipping_service}' "+ ((typeof(idEbayCarrier) != "undefined"  && idEbayCarrier == "{$carrier.shippingService|escape:'htmlall':'UTF-8'}")? 'selected="selected"' : '')  +" value='{$carrier.shippingService|escape:'htmlall':'UTF-8'}'>{$carrier.description|escape:'htmlall':'UTF-8'}</option>";

			{/if}

			{/foreach}
			createSelecstShipping += "</select></div></td>";
			// end carrier eBay


			// extrafree
			createSelecstShipping += "<td style='visibility: hidden;'><label>{l s='Additional item cost' mod='ebay'}</label><div class='popin_ship_cost'></div><div class='input-group'><span class='input-group-addon'> {$configCurrencysign|escape:'htmlall':'UTF-8'}</span><input class='extrafee form-control' "+ ((typeof(additionalFee) != "undefined" && additionalFee > 0) ? 'value="'+additionalFee+'"' : '')  +" min = '0' name='"+ (currentName == 'domesticShipping' ? 'extrafee' : 'extrafee_international') +"["+ lastId +"]' type='number' step='0.01'></div>"+ (currentName == 'domesticShipping' ? '<div class="help-block">' : '<p>') +" <a class='kb-help link_ship_cost' style='width: auto;background-image: none;' data-errorcode='{$help_ship_cost.error_code}' data-module='ebay' data-lang='{$help_ship_cost.lang}' module_version='{$help_ship_cost.module_version}' prestashop_version='{$help_ship_cost.ps_version}' href='' target='_blank' ></a><a class='kb-help'  data-errorcode='{$help_ship_cost.error_code}' data-module='ebay' data-lang='{$help_ship_cost.lang}' module_version='{$help_ship_cost.module_version}' prestashop_version='{$help_ship_cost.ps_version}' href='' target='_blank' ></a>"+ (currentName == 'domesticShipping' ? '</div>' : '</p>') +"</td>";
			// end extrafree

			// trash
			var atrash = $('<a class="ebay_trash btn btn-default btn-sm pull-right"><i class="icon-trash"></i></a>');
			// var imgtrash = $('<img>').attr('src', '../img/admin/delete.gif');
			// atrash.append(imgtrash);
			atrash.click(function() {
				$(this).closest('table').remove();
				return false;
			});
			var trash = $("<td style='visibility:hidden'>").append(atrash);
			// end trash

			createSelecstShipping += "</tr>";
			createSelecstShipping = $(createSelecstShipping).append(trash);
			if (hasValues) {
				createSelecstShipping = $("<table data-nb='"+lastId+"' class='table'>").append(createSelecstShipping);
			}
			else {
				createSelecstShipping = $("<table data-nb='"+lastId+"' class='table'>").append(createSelecstShipping);
			}

			current.append(createSelecstShipping);
			if (hasValues) {
				displayEbayCarrier(current.find('.prestaCarrier').eq(nbSelect));
				processEbayCarrier(current.find('.eBayCarrier').eq(nbSelect));
			}

			$('#domesticShipping select').unbind().change(function(){
				if ($(this).attr('class') == 'prestaCarrier')
					displayEbayCarrier($(this));
				else if ($(this).attr('class') == 'eBayCarrier')
					processEbayCarrier($(this));
			});
		}

		function displayEbayCarrier(select)
		{
			var valSelect = $(select).find('option').filter(":selected").text();
			var div = $(select).parent('div');
			var hasA = div.siblings('a').length;
			var tr = div.parent('td').parent('tr');
			var message = "{l s='Between 0 and ' mod='ebay'}";
			if ($(select).val().length > 1)
			{
				var contentA = $(select).find('option').filter(":selected").attr('data-realname');
				var contentAprice = $(select).find('option').filter(":selected").attr('data-maxprice');
				div.fadeOut(400, function() {
					$(this).hide();
					if (hasA > 0)
					{
						div.siblings('a').html(contentA).fadeIn();
					}
					else
					{
						var elementA = $('<a class="presta_shipping_text d-b">');
						elementA.append(contentA);
						div.after(elementA);
						tr.find("input").attr('max',contentAprice);
						tr.find(".link_ship_cost").text(message + contentAprice);
						elementA.click(function()
						{
							$(this).fadeOut(400, function(){
								$(this).siblings('div').fadeIn();
								tr.children('td').not(":first").css('visibility', 'hidden');
								checkShippingConfiguration(tr.closest('table'));
							});
							return false;
						});
					}
					var nextTd = tr.children('td').eq(1);
					nextTd.children('p').children('strong').text(' '+valSelect+' ');
					nextTd.css('visibility', 'visible');
				});
			}
			else
			{
				if (hasA > 0)
					div.siblings('a').fadeOut();
				tr.children('td').not(":first").css('visibility', 'hidden');
			}

			processEbayCarrier(div.parent().next().find('select'));
			cleanShippings();
			checkShippingConfiguration(div.closest('table'));


		}

		function processEbayCarrier(select)
		{
			var div = $(select).parent('div');
			var id_service = $(select).find('option:selected').attr('data-id');
			var hasA = div.siblings('a').length;
			var tr = div.parent('td').parent('tr');
			var idDivParent = tr.parents('div').attr('id');

			if ($(select).val() != 0)
			{
				var contentA = $(select).find('option').filter(":selected").text();
				var newHtmlP = div.siblings('p').attr('data-validate');
				var oldHtmlP = div.siblings('p').html();

				div.fadeOut(400, function(){
					$(this).hide();
					if (hasA > 0)
					{
						div.siblings('a').html(contentA).fadeIn();
					}
					else
					{
						var elementA = $('<a class="ebay_shipping_text d-b" data-id=\'' + id_service + '\' >');
						elementA.append(contentA);
						div.after(elementA);
						elementA.click(function()
						{
							tr.children('td:eq(3), td:eq(2)').css('visibility', 'hidden');
							$(this).fadeOut(400, function() {
								$(this).siblings('div').fadeIn();
							});
							div.siblings('p').html(oldHtmlP);
							checkShippingConfiguration(tr.closest('table'));
							return false;
						});
					}
					tr.children('td').css('visibility', 'visible');
					div.siblings('p').html(newHtmlP);
				});
			}
			checkShippingConfiguration(tr.closest('table'))
		}
		//]]>

		{literal}
		function addShippingFee(show, internationalOnly, idEbayCarrier, idPSCarrier, additionalFee) {
		{/literal}
			var currentShippingService = -1;
			var internationSuffix = '';

			if(internationalOnly == 1)
			{literal}{{/literal}
				var lastId = $('#internationalCarrier .internationalShipping').length;
				internationSuffix = '_international';
			}
			else
			{literal}{{/literal}

				var lastId = $('#nationalCarrier tr').length;
				internationSuffix = '';
			}


			if(additionalFee == undefined)
				additionalFee = '';

			var stringShippingFee = "<tr class='onelineebaycarrier'>";
			stringShippingFee += "<td>{l s='Choose your eBay carrier' mod='ebay'}</td>";
			stringShippingFee += 	"<td>";
			stringShippingFee += 		"<select name='ebayCarrier"+internationSuffix+"["+lastId+"]' id='ebayCarrier_"+lastId+"'>";
			{foreach from=$eBayCarrier item=carrier}
			currentShippingService = '{$carrier.shippingService|escape:'htmlall':'UTF-8'}';
			//check for international
			if((internationalOnly == 1 && '{$carrier.InternationalService|escape:'htmlall':'UTF-8'}' === 'true') || (internationalOnly == 0 && '{$carrier.InternationalService|escape:'htmlall':'UTF-8'}' !== 'true') )
			{literal}{{/literal}
				if(currentShippingService == idEbayCarrier)
				{literal}{{/literal}
					stringShippingFee += 		"<option value='{$carrier.shippingService|escape:'htmlall':'UTF-8'}' selected='selected'>{$carrier.description|escape:'htmlall':'UTF-8'}</option>";
				}
				else
				{literal}{{/literal}
					stringShippingFee += 		"<option value='{$carrier.shippingService|escape:'htmlall':'UTF-8'}'>{$carrier.description|escape:'htmlall':'UTF-8'}</option>";
				}
			}
			{/foreach}
			stringShippingFee += 		"</select>";
			stringShippingFee += 	"</td>";
			stringShippingFee += 	"<td>";
			stringShippingFee += 		"{l s='Associate it with a PrestaShop carrier' mod='ebay'}";
			stringShippingFee += 	"</td>";
			stringShippingFee += 	"<td>";
			stringShippingFee += 		"<select name='psCarrier"+internationSuffix+"["+lastId+"]' id='psCarrier_"+lastId+"'>";
													{foreach from=$psCarrier item=carrier}
			currentShippingService = {$carrier.id_carrier|escape:'htmlall':'UTF-8'};
			if(currentShippingService == idPSCarrier)
			{literal}{{/literal}
				stringShippingFee += 		"<option value='{$carrier.id_carrier|escape:'htmlall':'UTF-8'}' selected='selected'>{$carrier.name|escape:'htmlall':'UTF-8'}</option>";
			}
			else
			{literal}{{/literal}
				stringShippingFee += 		"<option value='{$carrier.id_carrier|escape:'htmlall':'UTF-8'}'>{$carrier.name|escape:'htmlall':'UTF-8'}</option>";
			}
													{/foreach}
			stringShippingFee += 		"</select>";
			stringShippingFee += 	"</td>";
			stringShippingFee += 	"<td>";
			stringShippingFee += 		"{l s='Add extra fee for this carrier' mod='ebay'} ";
			stringShippingFee += 	"<td>";
			stringShippingFee += 	"<td>";
			stringShippingFee += 		"<input class = 'extrafee' type='text' name='extrafee"+internationSuffix+"["+lastId+"]' value='"+additionalFee+"'>";
			stringShippingFee += 	"</td>";
			stringShippingFee += 	"<td>";
			stringShippingFee += 	"<img src='../img/admin/delete.gif' title='Delete' class='deleteCarrier' />";
			stringShippingFee += 	"</td>";
			stringShippingFee += "</tr>";

			if(show == 1)
				$('#nationalCarrier tr:last').after(stringShippingFee);
			else
				return stringShippingFee;
		}




		jQuery(document).ready(function($)
		{literal}{{/literal}
			/* INIT */
			{foreach from=$existingNationalCarrier item=nationalCarrier}
				{if isset($nationalCarrier.ps_carrier) && isset($nationalCarrier.ebay_carrier)}
					addShippingFee(1, 0, '{$nationalCarrier.ebay_carrier|escape:'htmlall':'UTF-8'}', '{$nationalCarrier.ps_carrier|escape:'htmlall':'UTF-8'}', '{$nationalCarrier.extra_fee|escape:'htmlall':'UTF-8'}');
				{/if}
			{/foreach}

			{foreach from=$existingNationalCarrier item=nationalCarrier key=i}
				{if isset($nationalCarrier.ps_carrier) && isset($nationalCarrier.ebay_carrier)}
				addShipping('domesticShipping', '{$nationalCarrier.ps_carrier|escape:'htmlall':'UTF-8'}', '{$nationalCarrier.ebay_carrier|escape:'htmlall':'UTF-8'}', '{$nationalCarrier.extra_fee|escape:'htmlall':'UTF-8'}', '{$i|escape:'htmlall':'UTF-8'}', '{$nationalCarrier.id_zone|escape:'htmlall':'UTF-8'}');
				{/if}
			{foreachelse}
				addShipping('domesticShipping');
			{/foreach}

			/* EVENTS */
			bindElements();
		});



		function cleanShippings()
		{
			$('#domesticShipping table tr').each(function(index, e){
				$(e).find('a.ebay_shipping_text').eq(1).remove();
			});
			$('#internationalShipping table tr').each(function(index, e){
				$(e).find('a.ebay_shipping_text').eq(1).remove();
			});

		}

		function checkGlobalShippingConfiguration()
		{
			$('#domesticShipping table').each(function(index, e){
				checkShippingConfiguration($(e));
			});

		}

		/**
		 * Check if a configuration is well configured
		 * @param el jQuery Element : Table}
		 * @param  Boolean isInternational [description]
		 * @return Boolean                  [description]
		 */
		{literal}
		function checkShippingConfiguration(el)
		{
			var is_configured = true;
			var isInternational = true;
			//Check if international or domestic shipping
			if(el.closest('#domesticShipping').length == 1)
				isInternational = false;

			//Check shipping configuration
			if(isInternational)
			{
				if(el.find('input').filter(':checked').length == 0)
					is_configured = false;
			}

			if(el.find('.prestaCarrier').val() == '')
				is_configured = false;

			if(el.find('.eBayCarrier').val() == '')
				is_configured = false;

			if(is_configured)
				el.removeClass('shipping_not_configured').addClass('shipping_configured');
			else
				el.removeClass('shipping_configured').addClass('shipping_not_configured');


			bindElements();
			return is_configured;


		}
		{/literal}

		function bindElements()
		{literal}{{/literal}

			$('#nationalCarrier .deleteCarrier').unbind().click(function(){literal}{{/literal}
				$(this).parent().parent().remove();
			});
			$('.addExcludedZone').unbind().click(function(){literal}{{/literal}
				var excludedButton = $(this);
				var excluded = getExcludeShippingLocation(excludedButton.parent());
				$(this).before(excluded);
			});


			$('#addNationalCarrier').unbind().click(function(){literal}{{/literal}
				addShippingFee(1, 0);
				bindElements();
			});


			$('#createlist').unbind().click(function(){literal}{{/literal}
				showExcludeLocation();
			});

			$('.showCountries').unbind().click(function(){literal}{{/literal}
				$(this).hide().parent().find('.listcountry').show();
			});

			$('#domesticShipping select, #internationalShipping select').unbind().change(function(){
				if ($(this).attr('class') == 'prestaCarrier')
					displayEbayCarrier($(this));
				else if ($(this).attr('class') == 'eBayCarrier')
					processEbayCarrier($(this));
			});

			$('#domesticShippingButton').unbind().click(function(){
				addShipping($(this).attr('id').replace('Button', ''));
				checkGlobalShippingConfiguration();
				return false;
			});

			$(document).on('click', '#domesticShippingButton', checkCountShippingNational);

            checkCountShippingNational();

			function checkCountShippingNational(){
				var shipping = $('.shipping_not_configured');
				if (shipping.length >= 4){
					$('#domesticShippingButton').hide();
					$('#transportDomesticWarning').show();
				}
			};

			{literal}
			$('.shipping_destinations input').unbind().click(function(el){
				checkShippingConfiguration($(this).closest('table'));
			});
			{/literal}
		}
	</script>
	<style>
	{literal}
		#domesticShipping table {
			border: 1px solid #CCCED7;
		}

		#domesticShipping table td {
			padding: 12px;
		}


		#domesticShipping table p.label {
			color: #000;
		}

		.excludeCountry input {
			margin-right:5px;
		}

		.allregion tr td {
			vertical-align: top;
		}

		.table tr td {
			border-bottom:none;
		}

		.internationalShipping input[type="checkbox"] {
			margin-right: 2px;
			margin-left: 0px;
			margin-top: -3px;
		}

		.unquatre .onelineebaycarrier select {
			width:140px;
		}

		#internationalShipping ul li {
			padding: 10px 5px 0 0;
		}
	{/literal}
	</style>

	<div style="display:none; position:absolute; width:500px; padding:20px 70px 20px 20px; left:30%;background-color:#FFF;border:4px solid #555;" id="warningOnCarriersContainer">
		<div class="close"><img src="" alt=""></div>
		<p class="error" id="warningOnCarriers" style="width:500px;">
		{l s='The Prestashop carriers added through a module are not configurable with the eBay integration.' mod='ebay'}<br/><br/>
		{l s='To overcome this issue, you can add a new carrier in your Prestashop back office and disable it from viewing in the Prestashop front office' mod='ebay'} <br/><br/>
		{foreach from=$psCarrierModule item=carrier}
			- <b>{$carrier.name|escape:'htmlall':'UTF-8'}</b><br/>
		{/foreach}
		</p>
		<div class="fancyboxeBayClose"  style="text-align:center; margin-top:10px;font-weight:bold;cursor:pointer;">{l s='Close' mod='ebay'}</div>
	</div>
	<!--script type="text/javascript" src="../js/jquery/jquery.fancybox-1.3.4.js"></script-->
	<script>
		{literal}
		$(document).ready(function() {
			if(!typeof $.fancybox == 'function') {
				$(".fancyboxeBay").fancybox({
					maxWidth	: '500px',
					maxHeight	: '300px',
					fitToView	: false,
					autoSize	: false,
					closeClick	: false,
					openEffect	: 'none',
					closeEffect	: 'none'
				});
			}
			else{
				$(".fancyboxeBay").click(function(){
					$("#warningOnCarriersContainer").fadeIn();
				});
				$(".fancyboxeBayClose").click(function(){
					$(this).parent().fadeOut();
				});
			}
		});
		var message_cost = "{/literal}{l s='Between 0 and ' mod='ebay'}{literal}";
		$('.extrafee').live('keyup', '.js_numbers_only', function(event) {

			var max = parseFloat($(this).attr('max'));
			console.log(max);
			if ($(this).val() > max ) {

				$(this).val(max);
			} else if ($(this).val() < 0) {
				$(this).val(0);
			}
		});
		$('.extrafee').live('focus',function() {
			$(this).parent().find('.popin_ship_cost').text(message_cost + $(this).attr('max'));
			$(this).parent().find('.popin_ship_cost').css('display','block');
		});

		$('.extrafee').live('focusout',function() {
			$(this).parent().find('.popin_ship_cost').css('display','none');
		});

		{/literal}
	</script>

	<fieldset style="margin-top:10px;">
		<div class="panel-heading">
			<span data-dialoghelp="#DomShipp" data-inlinehelp="{l s='You must specify at least one domestic shipping method. ' mod='ebay'}">
				{l s='Domestic shipping' mod='ebay'}
			</span>
		</div>

		<div id="domesticShipping"></div>

		<a id="domesticShippingButton" class="btn btn-default">
			<i class="icon-plus"></i>
			<span>
				{l s='Add new domestic carrier' mod='ebay'}
			</span>
		</a>
		<div class="alert alert-warning" style="display: none;" id="transportDomesticWarning">
			<button type="button" class="close" data-dismiss="alert">&#215;</button>
			{l s='Maximum transporteur reached' mod='ebay'}
		</div>
	</fieldset>

	<div id="buttonEbayShipping" class="panel-footer">
		<input class="primary button" name="submitSave" type="hidden"  value="{l s='Save and continue' mod='ebay'}"/>
		<button class="btn btn-default pull-right" type="submit" >
			<i class="process-icon-save"></i>
			{l s='Save' mod='ebay'}
		</button>
	</div>
</form>
