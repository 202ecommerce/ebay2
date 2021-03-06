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
	<script type="text/javascript">
		// <![CDATA[

		function checkCountShippingInternational(){
			var shipping = $('#internationalShipping table');
			if (shipping.length >= 5){
				$('#internationalShippingButton').hide();
				$('#transportInterWarning').show();
			}
		}


		function addInterShipping(currentName, idPSCarrier, idEbayCarrier, additionalFee, nbSelect, id_zone, zone) {
			var lastId = 0;
			var current = $("#"+currentName);
			var hasValues = (typeof(idPSCarrier) != "undefined" && typeof(idEbayCarrier) != "undefined" && typeof(additionalFee) != "undefined");
			var selected_services = new Array();
			$('#internationalShipping .linked a').each(function(){
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
							createSelecstShipping += "<option "+ (testPSCarrier ? 'selected="selected"' : '')  +" data-realname='{$zone.name|escape:'htmlall':'UTF-8'}, {$carrier.name|escape:'htmlall':'UTF-8'}' value='{$carrier.id_carrier|escape:'htmlall':'UTF-8'}-{$zone.id_zone|escape:'htmlall':'UTF-8'}' data-maxprice='{$carrier.price|escape:'htmlall':'UTF-8'}'>{$carrier.name|escape:'htmlall':'UTF-8'}</option>";
							if (testPSCarrier) {
								var valuePSCarrier = " {$carrier.name|escape:'htmlall':'UTF-8'} ";
							}
						{/foreach}
					createSelecstShipping += "</optgroup>";
				{/if}
			{/foreach}
			createSelecstShipping += "</select><span> {l s='Start by choosing a PrestaShop carrier to replicate on eBay' mod='ebay'}</span></div>";

			createSelecstShipping += "</td>";

			// end carrier Prestashop

			// Carrier eBay
			createSelecstShipping += "<td class='linked "+ (currentName == 'domesticShipping' ? '' : 'big-linked') +"'' style='visibility:hidden'><label data-validate='{l s='Linked to eBay' mod='ebay'}'>{l s='Linked' mod='ebay'}"+(hasValues ? valuePSCarrier : '')+"{l s='with an eBay carrier' mod='ebay'}</label><div><select name='"+ (currentName == 'domesticShipping' ? 'ebayCarrier' : 'ebayCarrier_international') +"[" + lastId + "]' class='eBayCarrier'><option value=''>{l s='Select eBay carrier' mod='ebay'}</option>";
			{foreach from=$eBayCarrier item=carrier}
				{if isset($carrier.id_shipping_service)}
            if (('{$carrier.InternationalService|escape:'htmlall':'UTF-8'}' == 'true' && currentName == 'internationalShipping')
                && selected_services.indexOf('{$carrier.id_shipping_service}') == -1)
                createSelecstShipping += "<option data-id='{$carrier.id_shipping_service}' " + ((typeof(idEbayCarrier) != "undefined"  && idEbayCarrier == "{$carrier.shippingService|escape:'htmlall':'UTF-8'}")? 'selected="selected"' : '')  +" value='{$carrier.shippingService|escape:'htmlall':'UTF-8'}'>{$carrier.description|escape:'htmlall':'UTF-8'}</option>";
				{/if}

			{/foreach}
			createSelecstShipping += "</select></div></td>";
			// end carrier eBay

			if (currentName == 'internationalShipping')
			{
				createSelecstShipping += "<td style='visibility:hidden;clear:left;' class='shipping_destinations'><label class='d-b'>{l s='Select the countries you will ship to' mod='ebay'}</label>";
				createSelecstShipping += "<ul style='float:left'>";
				{foreach from=$internationalShippingLocations item=shippingLocation name=loop}
					{if $smarty.foreach.loop.index%4 == 0 && $smarty.foreach.loop.index != 0}
						createSelecstShipping += "</ul><ul style='float:left'>";
					{/if}
					if(typeof(zone) != "undefined" && zone.indexOf('{$shippingLocation.location|escape:'htmlall':'UTF-8'}') != -1)
					{
						createSelecstShipping += '<li><input type="checkbox" checked="checked" name="internationalShippingLocation['+lastId+'][{$shippingLocation.location|escape:'htmlall':'UTF-8'}] value="{$shippingLocation.location|escape:'htmlall':'UTF-8'}"> {$shippingLocation.description|escape:'htmlall':'UTF-8'}</li>';
					}
					else
					{
						createSelecstShipping += '<li><input type="checkbox" name="internationalShippingLocation['+lastId+'][{$shippingLocation.location|escape:'htmlall':'UTF-8'}] value="{$shippingLocation.location|escape:'htmlall':'UTF-8'}"> {$shippingLocation.description|escape:'htmlall':'UTF-8'}</li>';
					}
				{/foreach}
				createSelecstShipping += "</ul></td>";
			}

			// extrafree
			createSelecstShipping += "<td style='visibility:hidden;'><label>{l s='Additional item cost' mod='ebay'}</label><div class='popin_ship_cost'></div><div class='input-group'><span class='input-group-addon'>{$configCurrencysign|escape:'htmlall':'UTF-8'}</span><input class='extrafee form-control' "+ ((typeof(additionalFee) != "undefined" && additionalFee > 0) ? 'value="'+additionalFee+'"' : '')  +" min = '0' name='"+ (currentName == 'domesticShipping' ? 'extrafee' : 'extrafee_international') +"["+ lastId +"]' type='number' step='0.01'></div>"+ (currentName == 'domesticShipping' ? '<div class="help-block">' : '<p>') +" <span class='link_ship_cost' style='width: auto;background-image: none;' ></span>"+ (currentName == 'domesticShipping' ? '</div>' : '</p>') +"</td>";
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

			$('#internationalShipping select').unbind().change(function(){
				if ($(this).attr('class') == 'prestaCarrier')
					displayEbayCarrier($(this));
				else if ($(this).attr('class') == 'eBayCarrier')
					processEbayCarrier($(this));
			});
		}



		//]]>

		{literal}
		function addShippingFeeInter(show, internationalOnly, idEbayCarrier, idPSCarrier, additionalFee){
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


		function getShippingLocation(lastId, zone)
		{literal}{{/literal}
			var string = '';
			{foreach from=$internationalShippingLocations item=shippingLocation}
				if(zone != undefined && zone.indexOf('{$shippingLocation.location|escape:'htmlall':'UTF-8'}') != -1)
				{literal}{{/literal}
				string += '<div class="shippinglocationOption"><input type="checkbox" checked="checked" name="internationalShippingLocation['+lastId+'][{$shippingLocation.location|escape:'htmlall':'UTF-8'}] value="{$shippingLocation.location|escape:'htmlall':'UTF-8'}">{$shippingLocation.description|escape:'htmlall':'UTF-8'}</div>';
				}
				else
				{literal}{{/literal}
				string += '<div class="shippinglocationOption"><input type="checkbox" name="internationalShippingLocation['+lastId+'][{$shippingLocation.location|escape:'htmlall':'UTF-8'}] value="{$shippingLocation.location|escape:'htmlall':'UTF-8'}">{$shippingLocation.description|escape:'htmlall':'UTF-8'}</div>';
				}

			{/foreach}
			return string;
		}


		function addInternationalShippingFee(idEbayCarrier, idPSCarrier, additionalFee, zone, zoneExcluded)
		{literal}{{/literal}
			var lastId = $('#internationalCarrier .internationalShipping').length;

			var string = "<div class='internationalShipping' data-id='"+lastId+"'>";
			string += "<table class='table'>";
			string += "<tr>";
			string += addShippingFeeInter(0, 1, idEbayCarrier, idPSCarrier, additionalFee);
			string += "</tr>";
			string += "</table>";
			string += "<label>{l s='Add country you will ship to' mod='ebay'}</label>";
			string += "<div class='margin-form'	>"+getShippingLocation(lastId, zone)+"</div>";
			string += "<div style='width:100%;clear:both'></div>";
			string += '</div>';

			$('#internationalCarrier div.internationalShipping:last').after(string);
		}

		function excludeLocation()
		{literal}{{/literal}
			var string = '<input type="hidden" value="1" name="excludeLocationHidden"/>';
			string += '<table class="allregion table">';

			{foreach from=$excludeShippingLocation.all item=region key=regionvalue name=count}
				{if $smarty.foreach.count.index % 4 == 0}
					string += "<tr>";
				{/if}
				string += "<td>";
				string += "<div class='excludedLocation'>";
				string += "<div class='checkbox'>";
				string += "<label class='control-label' for='excludeLocation[{$regionvalue|escape:'htmlall':'UTF-8'}]'>";
				string += "<input type='checkbox' id='excludeLocation[{$regionvalue|escape:'htmlall':'UTF-8'}]' name='excludeLocation[{$regionvalue|escape:'htmlall':'UTF-8'}]' {if in_array($regionvalue, $excludeShippingLocation.excluded)} checked='checked'{/if}/> {$region.description|escape:'htmlall':'UTF-8'}";
				string += "</label>";
				string += "</div>";
				string += "<span class='showCountries' data-region='{$regionvalue|escape:'htmlall':'UTF-8'}'>({l s='Show all countries' mod='ebay'})</span>";
				string += "<div class='listcountry'></div>";
				string += "</div>";
				string += "</td>";

				{if $smarty.foreach.count.index % 4 == 3}
					string += "</tr>";
				{/if}
			{/foreach}
			string += "</table>";



			return string;
		}

		jQuery(document).ready(function($)
		{literal}{{/literal}
			/* INIT */

			{literal}
			var zone = new Array();
			var zoneExcluded = new Array();
			{/literal}
			{foreach from=$existingInternationalCarrier item=internationalCarrier}
				zone = [];
				zoneExcluded = [];
				{foreach from=$internationalCarrier.shippingLocation item=shippingLocation}
					zone.push('{$shippingLocation.id_ebay_zone|escape:'htmlall':'UTF-8'}');
				{/foreach}
				addInternationalShippingFee('{$internationalCarrier.ebay_carrier|escape:'htmlall':'UTF-8'}', '{$internationalCarrier.ps_carrier|escape:'htmlall':'UTF-8'}', '{$internationalCarrier.extra_fee|escape:'htmlall':'UTF-8'}', zone, zoneExcluded);
			{/foreach}


			showExcludeLocation();


			var zone = new Array();
			{foreach from=$existingInternationalCarrier item=internationalCarrier key=i}
				zone = [];
				{foreach from=$internationalCarrier.shippingLocation item=shippingLocation}
					zone.push('{$shippingLocation.id_ebay_zone|escape:'htmlall':'UTF-8'}');
				{/foreach}
				{if isset($internationalCarrier.ebay_carrier) && isset($internationalCarrier.ps_carrier)}
					addInterShipping('internationalShipping', {$internationalCarrier.ps_carrier|escape:'htmlall':'UTF-8'}, '{$internationalCarrier.ebay_carrier|escape:'htmlall':'UTF-8'}', {$internationalCarrier.extra_fee|escape:'htmlall':'UTF-8'}, {$i|escape:'htmlall':'UTF-8'}, {$internationalCarrier.id_zone|escape:'htmlall':'UTF-8'}, zone);
				{/if}
			{foreachelse}
				addInterShipping('internationalShipping');
			{/foreach}


			/* EVENTS */
			bindElementsInter();
		});


		function showExcludeLocation()
		{literal}{{/literal}
			$('#nolist').fadeOut('normal', function()
			{literal}{{/literal}
				$('#list').html(excludeLocation());

			{literal}
			$('.showCountries').each(function()
			{
				var showcountries = $(this);
				$.ajax({
					url: formController,
					type: 'POST',
					data: {
					    region: $(this).attr('data-region'),
						profile: id_ebay_profile,
						ajax: true,
						action: 'GetCountriesLocation',
					},
					complete: function(xhr, textStatus) {

					},
					success: function(data, textStatus, xhr) {
						showcountries.parent().find('.listcountry').html(data);
					},
					error: function(xhr, textStatus, errorThrown) {
					//called when there is an error
					}
				});
			});

				bindElementsInter();

			});
			setTimeout(function(){
				cleanShippings();
				checkGlobalShippingConfiguration();
			}, 500);
			{/literal}

		}


		function bindElementsInter()
		{literal}{{/literal}
			$('#internationalCarrier .deleteCarrier').unbind().click(function(){literal}{{/literal}
				$(this).parent().parent().parent().parent().parent().remove();
			});

			$('.addExcludedZone').unbind().click(function(){literal}{{/literal}
				var excludedButton = $(this);
				var excluded = getExcludeShippingLocation(excludedButton.parent());
				$(this).before(excluded);
			});

			checkCountShippingInternational();

			$('#addInternationalCarrier').unbind().click(function(){literal}{{/literal}
				addInternationalShippingFee();
				bindElementsInter();
			});

			$('#createlist').unbind().click(function(){literal}{{/literal}
				showExcludeLocation();
			});

			$('.showCountries').unbind().click(function(){literal}{{/literal}
				$(this).hide().parent().find('.listcountry').show();
			});



			$('#internationalShippingButton').unbind().click(function(){
				addInterShipping($(this).attr('id').replace('Button', ''));
				checkGlobalShippingConfiguration();
				checkCountShippingInternational();
				return false;
			});



			{literal}
			$('.shipping_destinations input').unbind().click(function(el){
				checkShippingConfiguration($(this).closest('table'));
			});
			{/literal}
		}
	</script>
	<style>
	{literal}

		#internationalShipping table {
			padding: 0 5px 10px 10px;
			-webkit-border-radius: 10px;
			-moz-border-radius: 10px;
			border-radius: 10px;
		}

		.internationalShipping {
			background-color: #FFF;
			border: 1px solid #AAA;
			margin-bottom: 10px;
		}

		.excludedLocation {
			float: left;
			min-height: 30px;
			margin-right: 10px;
			min-width: 140px;
		}

		.excludeCountry {
			/*height: 20px;*/
			margin-top: -10px;
			padding-left: 5px;
		}

		.excludeCountry input {
			margin-right: 5px;
		}

		.allregion tr td {
			vertical-align: top;
		}

		.showCountries {
			color: blue;
			font-size: 9px;
			padding-left: 14px;
			cursor: pointer;
		}

		.listcountry {
			margin-top: 10px;
			display: none;
		}

		.table tr td {
			border-bottom: none;
		}

		#list .table input[type="checkbox"] {
			margin: 3px 0 0 -20px;
		}

		.internationalShipping input[type="checkbox"] {
			margin-right: 2px;
			margin-left: 0px;
			margin-top: -3px;
		}

		.shippinglocationOption {
			float: left;
			padding-top: 2px;
			margin-right: 10px;
			margin-top: 3px;
			margin-bottom: 5px;
		}

		.unquatre .onelineebaycarrier select {
			width: 140px;
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


	<fieldset style="margin-top:10px">
		<div class="panel-heading">
			<span data-dialoghelp="#DomShipp" data-inlinehelp="{l s='To configure international shipping you must select countries to ship to' mod='ebay'}">{l s='International shipping' mod='ebay'}</span>
		</div>

		<div id="internationalShipping"></div>

		<a id="internationalShippingButton" class="btn btn-default">
			<i class="icon-plus"></i>
			<span>
				{l s='Add new international carrier' mod='ebay'}
			</span>
		</a>
		<div class="alert alert-warning" style="display: none;" id="transportInterWarning">
			<button type="button" class="close" data-dismiss="alert">&#215;</button>
            {l s='Maximum transporteur reached' mod='ebay'}
		</div>
	</fieldset>

	<fieldset style="margin-top: 20px">
		<div class="panel-heading">
			<span data-inlinehelp="{l s='Check the boxes next to the countries you do not want to ship to.' mod='ebay'}">{l s='Exclude shipping locations' mod='ebay'}</span>
		</div>

		<label>
			{l s='Select any countries that you do not want to ship to' mod='ebay'} :
		</label>
		<div class="margin-form">
			<div id="nolist">
			</div>

			<div id="list">
			</div>
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
