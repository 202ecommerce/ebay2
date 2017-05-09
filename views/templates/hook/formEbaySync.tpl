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



<style>
	{literal}
	#button_ebay_sync1{background-image:url({/literal}{$path|escape:'urlencode'}{literal}views/img/ebay.png);background-repeat:no-repeat;background-position:center 90px;width:500px;height:191px;cursor:pointer;padding-bottom:100px;font-weight:bold;font-size:25px;}
			#button_ebay_sync2{background-image:url({/literal}{$path|escape:'urlencode'}{literal}views/img/ebay.png);background-repeat:no-repeat;background-position:center 90px;width:500px;height:191px;cursor:pointer;padding-bottom:100px;font-weight:bold;font-size:15px;}
	.informations{
		padding-bottom: 3px;margin-top: 8px;
	}
	#nbproducttosync
	{
		font-weight: bold;
	}

	{/literal}
</style>
<script>
	var module_time = "{$date|escape:'htmlall':'UTF-8'}";
	var showOptionals;
	var id_employee = {$id_employee|escape:'htmlall':'UTF-8'};
	var nbProducts = {$nb_products|escape:'htmlall':'UTF-8'};
	var nbProductsModeA = {$nb_products_mode_a|escape:'htmlall':'UTF-8'};
	var nbProductsModeB = {$nb_products_mode_b|escape:'htmlall':'UTF-8'};
	var bp_active = {$bp_active|escape:'htmlall':'UTF-8'};
	var l = {ldelim}
		'Attributes'						: "{l s='Attributes' mod='ebay'}",
		'Features'							: "{l s='Features' mod='ebay'}",
		'eBay Specifications'				: "{l s='eBay Specifications' mod='ebay'}",
		'Brand'								: "{l s='Brand' mod='ebay'}",
		'-- You have to select a value --'	: "{l s='-- You have to select a value --' mod='ebay'}",
		'Product Attributes'				: "{l s='Product Attributes' mod='ebay'}",
		'Reference'							: "{l s='Reference' mod='ebay'}",
		'EAN'								: "{l s='EAN' mod='ebay'}",
		'UPC'								: "{l s='UPC' mod='ebay'}",

		{rdelim};


	var conditions_data = new Array();
	{foreach from=$conditions key=type item=condition}
	conditions_data[{$type|escape:'htmlall':'UTF-8'}] = "{$condition|escape:'htmlall':'UTF-8'}";
	{/foreach}

	var possible_attributes = new Array();
	{foreach from=$possible_attributes item=attribute}
	possible_attributes[{$attribute.id_attribute_group|escape:'htmlall':'UTF-8'}] = "{$attribute.name|escape:'htmlall':'UTF-8'}";
	{/foreach}

	var possible_features = new Array();
	{foreach from=$possible_features item=feature}
	{if isset($feature.id_feature) && $feature.id_feature != ""}
	possible_features[{$feature.id_feature|escape:'htmlall':'UTF-8'}] = "{$feature.name|escape:'htmlall':'UTF-8'}";
	{/if}
	{/foreach}

	{literal}


	$(document).ready(function() {
		$("#ebay_sync_products_mode1").click(function() {
			nbProducts = nbProductsModeA;
			$("#catSync").hide("slow");
			$('#nbproducttosync').html(nbProducts);
		});
		$("#ebay_sync_products_mode2").click(function() {
			nbProducts = nbProductsModeB;
			$("#catSync").show("slow");
			$('#nbproducttosync').html(nbProducts);

		});
		$('.modifier_cat').on('click', function() {
			loadCategoriesConfig($(this).data('id'));
		});

		$('.js-next-popin').on('click', function() {

			var courant_page = $('.page_config_category.selected');


			var new_id =  parseInt(courant_page.attr('id'))+1;

			if(!bp_active || bp_active && $('select[name="payement_policies"] option:selected').val() != "" && $('select[name="return_policies"] option:selected').val() != "" )
			{
				$('.page_popin').html(''+new_id);
				courant_page.removeClass('selected').hide();
				courant_page.parent().find('div#'+new_id).addClass('selected').show();
			}
			if (new_id == 2) {
				if(!bp_active || bp_active && $('select[name="payement_policies"] option:selected').val() != "" && $('select[name="return_policies"] option:selected').val() != "" )
				{
					$('.js-prev-popin').show();
					$('#item_spec').html("<img src=\"../modules/ebay/views/img/ajax-loader-small.gif\" border=\"0\" />");
					loadCategoryItemsSpecifics($('#id_ebay_categories_real').val());
				}
			}
			if (new_id == 3) {
				$('.category_product_list_ebay').find('tbody').html("<img src=\"../modules/ebay/views/img/ajax-loader-small.gif\" border=\"0\" />");
				$('.category_ps_list').find('li').each(function( index ) {
					showProducts($( this ).attr('id'));
				});
				$('.category_product_list_ebay').find('img').remove();
			}
			if (new_id == 4) {
				var nb_annonces_to_job = 0;
				$('.category_product_list_ebay').find('.sync-product').each(function () {
					if ($(this).prop('checked')) {
						nb_annonces_to_job ++;
					}
				});
				$('.nb_annonces').html(nb_annonces_to_job);
				$('#last_page_categorie_ps').html('').append($('.category_ps_list').children());
				$('#last_page_categorie_ebay').html('').append($('.category_ebay').children().find('option:selected').last().text());
				$('#last_page_categorie_boutique').html('').append($('select[name="store_category"]').find('option:selected').text());
				$('#last_page_categorie_prix').html('').append($('#impact_prix').val());
				$('#last_page_business_p').html('').append($('select[name="payement_policies"]').find('option:selected').text());
				$('#last_page_business_r').html('').append($('select[name="return_policies"]').find('option:selected').text());
				$('.js-next-popin').hide();
				$('.js-save-popin').show();
			}

		});
		$('.js-prev-popin').on('click', function() {
			$('.js-next-popin').show();
			var courant_page = $('.page_config_category.selected');
			courant_page.removeClass('selected').hide();
			console.log(courant_page.attr('id'));
			var new_id =  parseInt(courant_page.attr('id'))-1;
			$('.page_popin').html(''+new_id);
			console.log(new_id);
			if(new_id == 1){
				$('.js-prev-popin').hide();
			}
			$('.js-save-popin').hide();
			console.log(courant_page.parent().find('#'+new_id));
			courant_page.parent().find('div#'+new_id).addClass('selected').show();

		});
		$('.js-save-category').on('click', function() {
			var data = $('#category_config').serialize();
			var ps_categories = new Array();
			$('#last_page_categorie_ps').find('li').each(function( index ) {
				ps_categories.push($( this ).attr('id'));
			});
			event.preventDefault();

			var url = module_dir + "ebay/ajax/saveConfigFormCategory.php?token=" + ebay_token + "&id_employee=" + id_employee + "&id_lang=" + id_lang + "&profile=" + id_ebay_profile + '&id_shop=' + id_shop +'&ps_categories='+ps_categories+ '&'+data;
			console.log(data);
				$.ajax({
					type: "POST",
					url: url,
					success: function (data) {
						location.reload();
					}
				});
		});

		$('.js-remove-item').live('click', function() {
			$(this).parent().remove();
			if($('ul.category_ps_list li').length == 0) {
				$('.js-next-popin').attr('disabled','disabled');
			}
		});

		$('.categorySync').change(function() {
			var id_product = $(this).val();
			$.ajax({
				cache: false,
				dataType: 'json',
				type: "POST",
				url: module_dir + "ebay/ajax/ActiveCategory.php?token=" + ebay_token + "&profile=" + id_ebay_profile + "&ebay_category=" + id_product + "&id_employee=" + id_employee + "&id_lang=" + id_lang,
				success: function(data) {
					$('.orhan_badge').html(data);
				}
			});
		});
		$('.modifier_cat').fancybox({
			'modal': true,
			'showCloseButton': false,
			'padding': 0,
			'parent': '#popin-container',
		});

		$('.js-save1-popin').on('click', function() {
			$('#popin-categorie-save').show();
		});
		$('.js-notsave').on('click', function() {
			event.preventDefault();
			$('#popin-categorie-save').hide();
		});

		$('.delete_cat').on('click', function() {
			var tr = $(this).parent().parent();
			var id_category = $(this).data('id')
			if (confirm(header_ebay_l['Are you sure you want to delete this category?'])) {
				$.ajax({
					cache: false,
					dataType: 'json',
					type: "POST",
					url: module_dir + "ebay/ajax/deleteConfigCategory.php?token=" + ebay_token + "&profile=" + id_ebay_profile + "&ebay_category=" + id_category,
					success: function (data) {
						tr.remove();
					}
				});
			}
		});

		$('.add_categories_ps li').live('click', function() {
			$('.category_ps_list').append($( this ));
			$('.category_ps_list').children().last().append('<button type="button" class="js-remove-item  btn btn-xs btn-danger pull-right" title="remove category"><i class="icon-trash"></i></button>');
			$('#divPsCategories').html('');
			$('.js-next-popin').removeAttr('disabled');
		});


		function loadCategoriesConfig(id_category)
		{
			var url = module_dir + "ebay/ajax/loadConfigFormCategory.php?token=" + ebay_token + "&id_lang=" + id_lang + "&profile=" + id_ebay_profile + '&id_shop=' + id_shop + '&id_category_ps=' + id_category;

			$.ajax({
				type: "POST",
				url: url,
				success: function (data) {

					var data = jQuery.parseJSON(data);


					$('#ps_category_list').hide();
					$('.category_ps_list').html('<li class="item" id="'+ data.categoryList['id_category'] +'">'+data.categoryList['name']+'</b> </li>');
					$('#form_product_to_sync').html(data.getNbSyncProducts);
					$('#form_variations_to_sync').html(data.getNbSyncProductsVariations);
					$('select[name="impact_prix[sing]"]').children('option[value="' + data.percent.sign + '"]').attr('selected', 'selected');
					$('select[name="impact_prix[type]"]').children('option[value="' + data.percent.type + '"]').attr('selected', 'selected');
					$('#impact_prix').val(data.percent.value);
					$('.category_ebay').html(data.categoryConfigList.var);
                    if(data.bp_policies != null){
                        $('select[name="payement_policies"]').children('option[value="' + data.bp_policies["id_payment"] + '"]').attr('selected', 'selected');
                        $('select[name="return_policies"]').children('option[value="' + data.bp_policies["id_return"] + '"]').attr('selected', 'selected');
                    }

					$('select[name="store_category"]').children('option[value="' + data.storeCategoryId + '"]').attr('selected', 'selected');
					$('.js-next-popin').removeAttr('disabled');

				}
			});

		}

		function loadCategoryItemsSpecifics(category_id)
		{

			$.ajax({
				cache: false,
				dataType: 'json',
				type: "POST",
				url: module_dir + "ebay/ajax/loadItemsSpecificsAndConditions.php?token=" + ebay_token + "&profile=" + id_ebay_profile + "&ebay_category=" + category_id + "&id_lang=" + id_lang,
				success: function(data) {
					$('#specifics-' + category_id + '-loader').hide();
					insertCategoryRow(category_id, data);
				}
			});
		}

		showOptionals = function showOptionals(category_id)
		{
			var nb_rows_to_add = $('tr.optional[category=' + category_id + ']').length;

			var first_td = $('#specifics-' + category_id + ' td:nth-child(1)');
			first_td.attr('rowspan', parseInt(first_td.attr('rowspan')) + nb_rows_to_add - 1);

			$('tr[category=' + category_id + ']').show();
			$('#switch-optionals-' + category_id).hide();

			return false;
		}
		function insertCategoryRow(category_id, data)
		{
			var has_optionals = false;
			var trs = '';
			var trs_optionals = '';

			// specifics
			var specifics = data.specifics;

			for (var i in specifics)
			{
				var specific = specifics[i];
				var tds = '<td>' + specific.name + '</td><td>';
				tds += '<select name="specific[' + specific.id + ']">';

				if (!parseInt(specific.required)) {
					tds += '<option value=""></option>';
				} else if (specific.name == 'K-type'){
					tds += '<option value="">Do not synchronise</option>';
				} else {
					tds += '<option value="">' + l['-- You have to select a value --'] + '</option>';
				}
				if (specific.selection_mode == 0 && specific.name != 'K-type')
				{
					if (!data.is_multi_sku || specific.can_variation)
					{
						tds += '<optgroup label="' + l['Attributes'] + '">';
						tds += writeOptions('attr', possible_attributes, specific.id_attribute_group);
						tds += '</optgroup>';
					}
					tds += '<optgroup label="' + l['Product Attributes'] + '">';
					tds += '<option value="brand-1" ' + (specific.is_brand == 1 ? 'selected' : '') + '>' + l['Brand'] + '</option>';
					tds += '<option value="reference-1" ' + (specific.is_reference == 1 ? 'selected' : '') + '>' + l['Reference'] + '</option>';
					tds += '<option value="ean-1" ' + (specific.is_ean == 1 ? 'selected' : '') + '>' + l['EAN'] + '</option>';
					tds += '<option value="upc-1" ' + (specific.is_upc == 1 ? 'selected' : '') + '>' + l['UPC'] + '</option>';
					tds += '</optgroup>';
					tds += '<optgroup label="' + l['Features'] + '">';
					tds += writeOptions('feat', possible_features, specific.id_feature);
					tds += '</optgroup>';
				} else if (specific.name == 'K-type') {
					tds += '<optgroup label="' + l['Features'] + '">';
					tds += writeOptions('feat', possible_features, specific.id_feature);
					tds += '</optgroup>';
				}
				if (specific.name != 'K-type') {
					tds += '<optgroup label="' + l['eBay Specifications'] + '">';
					tds += writeOptions('spec', specific.values, specific.id_specific_value);
					tds += '</optgroup>';
				}
				tds += '</select></td>';

				if (parseInt(specific.required))
					trs += '<tr ' + (i % 2 == 0 ? 'class="alt_row"' : '')+ 'category="'+ category_id + '">' + tds + '</tr>';
				else
				{
					trs_optionals += '<tr class="optional" ' + (parseInt(specific.required) ? '' : 'style="display:none"') + ' ' + (i % 2 == 0 ? 'class="alt_row"' : '') + 'category="'+ category_id + '">' + tds + '</tr>';

					if (!has_optionals)
						has_optionals = true;
				}
			}

			// Item Conditions
			var ebay_conditions = data.conditions;
			var alt_row = true;
			if(Object.keys(ebay_conditions).length > 0) {
				for (var condition_type in conditions_data) {
					var condition_data = conditions_data[condition_type];
					var tds = '<td><select name="condition[' + category_id + '][' + condition_type + ']">';

					for (var id in ebay_conditions)
						tds += '<option value="' + id + '" ' + ($.inArray(condition_type, ebay_conditions[id].types) >= 0 ? 'selected' : '') + '>' + ebay_conditions[id].name + '</option>';

					tds += '</td><td>' + condition_data + '</td>';
					trs += '<tr ' + (alt_row ? 'class="alt_row"' : '') + 'category="' + category_id + '">' + tds + '</tr>';

					alt_row = !alt_row;
				}
			}
			if (has_optionals)
				trs += '<tr id="switch-optionals-' + category_id + '"><td><a href="#" onclick="return showOptionals(' + category_id + ')">See optional items</a></td><td></td></tr>';

			var row = $('#item_spec');
			row.children('td:nth-child(1)').attr('rowspan', $(trs).length + 1);

			row.html('').append(trs + trs_optionals);

		}

		function writeOptions(value_prefix, options, selected_id)
		{
			var str = '';

			for (var id in options)
				str += '<option value="' + value_prefix + '-' + id + '" ' + (id == selected_id ? 'selected' : '') + '>' + options[id] + '</option>';

			return str;
		}



		function showProducts(id_category)
		{

            $.ajax({
                dataType: 'json',
                type: "POST",
                url: module_dir + 'ebay/ajax/getProducts.php?category=' + id_category + '&token=' + ebay_token + '&id_ebay_profile=' + id_ebay_profile,
                success: function (products) {

                    var str = '';
					if (products.length > 0) {
						for (var i in products) {
							var product = products[i];

							str += '<tr class="product-row ' + (i % 2 == 0 ? 'alt_row' : '') + '" category="' + id_category + '"> \
							<td>' + product.id + '</td> \
							<td>' + product.name + '</td> \
							<td class="ebay_center">' + (parseInt(product.stock) ? product.stock : '<span class="red">0</span>') + '</td> \
							<td class="ebay_center"> \
								<input name="showed_products[' + product.id + ']" type="hidden" value="1" /> \
								<input onchange="toggleSyncProduct(' + id_category + ')" class="sync-product" category="' + id_category + '" name="to_synchronize[' + product.id + ']" type="checkbox" ' + (product.blacklisted == 1 ? '' : 'checked') + ' /> \
							</td> \
						</tr>';
						}
						if (str != '') {
							str += '</table></td></tr>';
						}
					}
					console.log(str);

                    $('.category_product_list_ebay').find('tbody').append(str);
                }
            });


}

		$('#ps_category_autocomplete_input').keyup(function () {
			loadPsCategories($(this).val());
		});

		$(document).on('click', '*:not(#ps_category_list)', function() {
			if($('#ps_category_autocomplete_input').is(':visible')) {
				$('#divPsCategories').html('');
			}
		});


	});

	function changeCategoryMatch(level, id_categoris)
	{
		var id_category = 0;
		var levelParams = "&level1=" + $("#categoryLevel1-" + id_category).val();
		if (level > 1) levelParams += "&level2=" + $("#categoryLevel2-" + id_category).val();
		if (level > 2) levelParams += "&level3=" + $("#categoryLevel3-" + id_category).val();
		if (level > 3) levelParams += "&level4=" + $("#categoryLevel4-" + id_category).val();
		if (level > 4) levelParams += "&level5=" + $("#categoryLevel5-" + id_category).val();

		$.ajax({
			type: "POST",
			url: module_dir + 'ebay/ajax/changeCategoryMatch.php?token=' + ebay_token + '&id_category=' + id_category + '&time=' + module_time + '&level=' + level + levelParams + '&ch_cat_str=no category selected&profile=' + id_ebay_profile,
			success: function (data) {
				$(".category_ebay").html(data);
			}
		});
	}

	function loadPsCategories(search)
	{

		var url = module_dir + "ebay/ajax/loadAjaxCategories.php?token=" + ebay_token + "&id_lang=" + id_lang + "&profile=" + id_ebay_profile + '&id_shop=' + id_shop  +'&s=' + search;

		$.ajax({
			type: "POST",
			url: url,
			success: function (data) {
				var data = jQuery.parseJSON(data);
				var str = '<ul class="add_categories_ps">';
				$.each(data.categoryList, function( index, value ) {
					str += '<li id="'+ value.id_category +'">'+ value.name +'</li>';
				});
				str += '</ul>';
				$('#divPsCategories').html('').append(str);
			}
		});

	}

	{/literal}
</script>

<div id="resultSync" style="text-align: center; font-weight: bold; font-size: 14px;"></div>



	<fieldset style="border: 0">
		<a href="#popin-add-cat" class="js-popin btn btn-lg btn-success" {if $shipping_tab_is_conf}disabled="disabled"{/if}><span class="icon-plus" ></span> Ajouter</a>
		{if isset($img_alert) && !empty($img_alert)}
			<div class="warning big">
                {$img_alert['message']|escape:'htmlall':'UTF-8'}
                {if isset($img_alert.kb)}
					<a class="kb-help" data-errorcode="{$img_alert.kb.errorcode}" data-module="ebay" data-lang="{$img_alert.kb.lang}" module_version="{$img_alert.kb.module_version}" prestashop_version="{$img_alert.kb.prestashop_version}"></a>
				{/if}
            </div>
		{/if}
        {if isset($category_alerts) && !empty($category_alerts)}
            <div class="warning big">
                {$category_alerts|escape:'htmlall':'UTF-8'}
            </div>
        {/if}
		{*<h4>{l s='You\'re now ready to list your products on eBay.' mod='ebay'}</h4>
		<label style="width: 250px;">{l s='List all products on eBay' mod='ebay'} : </label><br /><br />
		<div class="margin-form">
			<input type="radio" size="20" name="ebay_sync_products_mode" id="ebay_sync_products_mode1" value="A" {if $is_sync_mode_b == false}checked="checked"{/if}/> <span data-inlinehelp="{l s='All items that have specified an eBay category will be listed.' mod='ebay'}">{l s='List all products on eBay' mod='ebay'}</span>
		</div>
		<div class="margin-form">
			<input type="radio" size="20" name="ebay_sync_products_mode" id="ebay_sync_products_mode2" value="B" {if $is_sync_mode_b == true}checked="checked"{/if}/> {l s='Sync the products only in selected categories' mod='ebay'}
		</div>
		<div class="clear both"></div>
		<label style="width: 250px;">{l s='Option' mod='ebay'} : </label><br /><br />
		<div class="margin-form">
			<input type="checkbox" size="20" name="ebay_sync_option_resync" id="ebay_sync_option_resync" value="1" {if $ebay_sync_option_resync == 1}checked="checked"{/if} /> <span data-inlinehelp="{l s='All other product properties will be stay the same.' mod='ebay'}">{l s='Only synchronise price and quantity' mod='ebay'}</span>
		</div>
		<div class="clear both"></div>
		<label>{l s='Sync mod' mod='ebay'} :	</label><br /><br />
		<div class="margin-form">
			<input type="radio" size="20" name="ebay_sync_mode" id="ebay_sync_mode_2" value="2" {if $ebay_sync_mode == 2}checked="checked"{/if}/> <span data-inlinehelp="{l s='Any changes that you make to listings in PrestaShop will also be applied on eBay.' mod='ebay'}">{l s='Sync new products and update existing listings' mod='ebay'}</span>
		</div>
		<div class="margin-form">
			<input type="radio" size="20" name="ebay_sync_mode" id="ebay_sync_mode_1" value="1" {if $ebay_sync_mode == 1}checked="checked"{/if}/> <span data-inlinehelp="{l s='This will only synchronisze products that are not yet listed on eBay.' mod='ebay'}">{l s='Only sync new products' mod='ebay'}</span>
		</div>*}
		<div style="display: block;" id="catSync">
			<table class="table tableDnD" cellpadding="0" cellspacing="0" width="90%">
				<thead>
					<tr class="nodrag nodrop">
						<th>{l s='Catégorie PrestaShop' mod='ebay'}</th>
						<th>{l s='Produits / variations' mod='ebay'}</th>
						<th>{l s='Exclusions manuelles' mod='ebay'}</th>
						<th>{l s='Impact prix' mod='ebay'}</th>
						<th>{l s='Catégorie eBay' mod='ebay'}</th>
						<th>{l s='Multi var' mod='ebay'}</th>
						<th>{l s='Annonces' mod='ebay'}</th>
						<th>{l s='Etat' mod='ebay'}</th>
						<th>{l s='Action' mod='ebay'}</th>
					</tr>
				</thead>
				<tbody>
					{if $categories|count == 0}
						<tr><td colspan="2">{l s='No category found.' mod='ebay'}</td></tr>
					{else}
						{foreach from=$categories item=category}
							<tr class="{$category.row_class|escape:'htmlall':'UTF-8'}">
								<td>{$category.name|escape:'htmlall':'UTF-8'}</td>
								<td>{if $category.category_multi == 'yes'}<b>{$category.nb_products|escape:'htmlall':'UTF-8'}</b>/{$category.nb_products_variations|escape:'htmlall':'UTF-8'}{else}{$category.nb_products|escape:'htmlall':'UTF-8'}/<b>{$category.nb_products_variations|escape:'htmlall':'UTF-8'}</b>{/if} </td>
								<td>{$category.nb_products_blocked|escape:'htmlall':'UTF-8'}</td>
								<td>{$category.price|escape:'htmlall':'UTF-8'}</td>
								<td>{$category.category_ebay|escape:'htmlall':'UTF-8'}</td>
								<td>{$category.category_multi|escape:'htmlall':'UTF-8'}</td>
								<td>{$category.annonces|escape:'htmlall':'UTF-8'}/
									{if $category.category_multi == 'yes'}{$category.nb_product_tosync|escape:'htmlall':'UTF-8'}{else}{$category.nb_variations_tosync|escape:'htmlall':'UTF-8'}{/if}</td>
								<td><input type="checkbox" class="categorySync" name="category[]" value="{$category.value|escape:'htmlall':'UTF-8'}" {$category.checked|escape:'htmlall':'UTF-8'} />
								<td><a href="#popin-add-cat" class="modifier_cat btn btn-lg btn-success" data-id="{$category.value}"><span ></span> Modifier</a>
									<a href="#" class="delete_cat btn btn-lg btn-success" data-id="{$category.value}"><span ></span>DELETE</a></td>
							</tr>
						{/foreach}
					{/if}
				</tbody>
			</table>
			{if $sync_1}
				<script>
					$(document).ready(function() {ldelim}
						eBaySync(1);
					{rdelim});
				</script>
			{/if}
			{if $sync_2}
				<script>
					$(document).ready(function() {ldelim}
						eBaySync(2);
					{rdelim});
				</script>
			{/if}
			{if $is_sync_mode_b}
				<script>
					$(document).ready(function() {ldelim}
						$("#catSync").show("slow");
						$("#ebay_sync_products_mode2").attr("checked", true);
					{rdelim});
				</script>
			{/if}
		</div><br />

	</fieldset>


<div id="popin-container">
	{* Category add modal *}
	<div id="popin-add-cat" class="popin popin-lg" style="display: none;">
		<div class="panel">
			<div  class="panel-heading">
				<i class="icon-plus"></i> Ajouter une annonce
				<span class="badge badge-success"><span class="page_popin" id="1">1</span> / 4</span>
			</div>

			<form action="" class="form-horizontal" method="post" id="category_config">
				<div id="1" class="page_config_category selected first_page_popin">
					<div class="form-group page">
						<label for="" class="control-label col-md-6" data-toggle="tooltip" data-placement="bottom" title="Pour des raisons de performances, il est recommandé de se limiter à 3 catégories par annonce.">
							Choisissez une ou plusieurs catégories PrestaShop :
						</label>

						<div class="input-group col-md-6" id="ps_category_list">
							<div id="ajax_choose_category">
								<div class="input-group">
									<input type="text" id="ps_category_autocomplete_input" name="ps_category_autocomplete_input" autocomplete="off" class="ac_input">
									<span class="input-group-addon"><i class="icon-search"></i></span>
								</div>
							</div>
							<div id="divPsCategories">
							</div>
						</div>

						<ul class="col-md-6 col-md-6 item-list category_ps_list">

						</ul>

					</div>

					<div class="form-group">
						<div class="col-md-push-6 col-md-6 alert alert-info alert-no-icon product_sync_info">
							<span>produits : <span class="badge badge-info" id="form_product_to_sync"></span></span>
							<span>variations : <span class="badge badge-info" id="form_variations_to_sync"></span></span>
						</div>
					</div>

					<div class="form-group">
						<label for="" class="control-label col-md-6">
							Choisissez une catégorie ebay :
						</label>
						<div class="input-group col-md-6 category_ebay">


						</div>
						<input type="hidden" name="category[0]" value="0">
					</div>

					<div class="form-group">
						<label for="" class="control-label col-md-6">
							Impact sur le prix :
						</label>
						<div class="input-group col-md-6">
							<div class="row">


									<select name="impact_prix[sign]" class="ebay_select">
										<option value="+">+</option>
										<option value="-">-</option>
									</select>
									<input type="text" id="impact_prix" name="impact_prix[value]" >
									<select name="impact_prix[type]" class="ebay_select">
										<option value="currency">€</option>
										<option value="percent">%</option>
									</select>



							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="" class="control-label col-md-6">
							Choisissez une catégorie de votre boutique eBay :
						</label>
						{if $storeCategories}
						<div class="input-group col-md-6">
							<select name="store_category" style="width: 200px;">
								{if empty($storeCategories)}
									<option disabled="disabled"  value="">{l s='Please create a return policy in the Sell section of My eBay' mod='ebay'}</option>
								{else}
									<option  value=""></option>
								{/if}
								{foreach from=$storeCategories item=storeCategory}
									<option value="{$storeCategory.ebay_category_id}" >{$storeCategory.name}</option>
								{/foreach}
							</select>
						</div>
						{/if}
					</div>
					{if $bp_active}
					<div class="form-group">
						<label for="" class="control-label col-md-6">
							Business Policies* :
						</label>
						<select name="return_policies" style="width: 200px;">
							{if empty($RETURN_POLICY)}
								<option disabled="disabled"  value="">{l s='Please create a return policy in the Sell section of My eBay' mod='ebay'}</option>
							{else}
								<option  value=""></option>
							{/if}
							{foreach from=$RETURN_POLICY item=RETURN}
								<option value="{$RETURN.id_bussines_Policie}" >{$RETURN.name}</option>
							{/foreach}
						</select>

						<label for="" class="control-label col-md-6">
							Business Policies* :
						</label>
						<select name="payement_policies" style="width: 200px;">
						{if empty($PAYEMENTS)}
							<option disabled="disabled" value="">{l s='Please create a payment policy in the Sell section of My eBay' mod='ebay'}</option>
						{else}
							<option  value=""></option>
						{/if}

						{foreach from=$PAYEMENTS item=PAYEMENT}
							<option  value="{$PAYEMENT.id_bussines_Policie}">{$PAYEMENT.name}</option>
						{/foreach}
						</select>
					</div>
					{/if}
					<div class="form-group" style="display: none">
						<label for="" class="control-label col-md-6">
							Exclure des produits :
						</label>
						<div class="input-group col-md-6">
							<input type="text" id="product_autocomplete_input" name="product_autocomplete_input" autocomplete="off" class="ac_input">
							<span class="input-group-addon"><i class="icon-search"></i></span>
						</div>
						<ul class="col-md-push-6 col-md-6 item-list">
							<li class="item">Sandales bleues <button type="button" class="btn btn-xs btn-danger pull-right" title="remove category"><i class="icon-trash"></i></button></li>
							<li class="item">Balerines à pois dorées <button type="button" class="btn btn-xs btn-danger pull-right" title="remove category"><i class="icon-trash"></i></button></li>
						</ul>
					</div>
				</div>
				<div id="2" class="page_config_category" style="display: none">
					<div class="form-group">
						<label for="" class="control-label col-md-6">
							Faites correspondre les caractéristiques PrestaShop avec celle d'eBay:
						</label>
						<div class="input-group col-md-12 category_spec_ebay">
							<table class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%;">
								<thead>
								<tr class="nodrag nodrop">
									<th style="width:20%">
										<span data-inlinehelp="Les premieres caractéristiques de produits sont obligatoires, et vous ne pourrez pas exporter vos produits sans les ajouter. Vous pouvez aussi ajouter des caractéristiques optionneles qui aideront l'acheteur à trouver vos objets. Dans le deuxième encart, renseignez l'état de vos objets">Caractéristiques d'objet</span><a class=" tooltip" target="_blank"> <img src="../img/admin/help.png" alt=""></a>
									</th>
									<th style="width:50%">
										Caractéristiques Prestashop
									</th>
								</tr>
								</thead>
								<tbody id="item_spec">
								</tbody>
							</table>
						</div>

					</div>
				</div>
				<div id="3" class="page_config_category" style="display: none">
					<div class="form-group">
						<label for="" class="control-label col-md-6">
							Faites correspondre les caractéristiques PrestaShop avec celle d'eBay:
						</label>
						<div class="input-group col-md-12 category_product_list_ebay">
							<table class="table tableDnD" width="80%" style="margin: auto">
								<thead>
								<tr class="product-row" category="5">
									<th class="">ID Produit</th>
									<th class="">Name</th>
									<th class="ebay_center ">Stock</th>
									<th class="ebay_center ">Déselectionnez les produits que vous ne voulez pas mettre
										en vente sur eBay
									</th>
								</tr>
								</thead>
								<tbody>

								</tbody>
							</table>
						</div>

					</div>
				</div>
				<div id="4" class="page_config_category" style="display: none">
					<div class="form-group">
						<label for="" class="control-label col-md-6">
							Config :
						</label>
					</div>
					<div class="input-group col-md-12 total_config_list_ebay">
						<div class="col-md-12">
							<br>
							 <b>Catégories PS : </b><span id="last_page_categorie_ps"> </span><br>
							 <b>Catégory eBay : </b><span id="last_page_categorie_ebay"></span><br>
							 <b>Impact prix : </b><span id="last_page_categorie_prix"> </span><br>
							 <b>Catégorie de votre boutique eBay : </b><span id="last_page_categorie_boutique"></span><br>
							<br>
							<h4>Business Policies</h4>
							 <b>Payement Policies : </b><span id="last_page_business_p"></span><br>
							 <b>Return Policies : </b><span id="last_page_business_r"> </span><br><br><br>
							L’ajout de ces produits se fait automatiquement dans les minutes qui viennent.
							<br>
							<br>
							<a href="#popin-categorie-save" class="js-save1-popin btn btn-lg btn-success">Save</a>

						</div>
					</div>

					<div id="popin-categorie-save" class="popin popin-sm" style="display: none;position: fixed;z-index: 1;left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgb(0,0,0);background-color: rgba(0,0,0,0.4);">
						<div class="panel" style=" background-color: #fefefe;padding: 20px;border: 1px solid #888;width: 80%;margin: 30% auto;">
							<div class="panel-heading">
								<i class="icon-trash"></i> Save Categories
							</div>
							<p>Cette action va ajouter/modifier <span class="nb_annonces"></span> annonces sur eBay.</p>

							<div class="panel-footer">
								<button class="js-notsave btn btn-default"><i class="process-icon-cancel"></i>Annuler</button>
								<button class="js-save-category btn btn-success pull-right"><i class="process-icon-save"></i>OK</button>
							</div>
						</div>
					</div>

				</div>
			</form>

			<div class="panel-footer">
				<button class="js-close-popin btn btn-default"><i class="process-icon-cancel"></i>Annuler</button>
				<button class="js-next-popin btn btn-primary pull-right"><i class="process-icon-next"></i>Suivant</button>

				<button class="js-prev-popin btn btn-primary pull-right" style="display: none"><i class="process-icon-next"  style="transform: rotateZ(180deg);transform-origin: 50% 45%;"></i>prev</button>
			</div>
			<div style="display: none">
				<select class="select_category_default" name="category" id="categoryLevel1-0" rel="0" style="font-size: 12px; width: 160px;" onchange="changeCategoryMatch(1,0);">
					<option value="0">no category selected</option>
					{foreach from=$ebayCategories item=ebayCategory}
						<option value="{$ebayCategory.id_ebay_category}" >{$ebayCategory.name}</option>
					{/foreach}
				</select>

			</div>
		</div>
	</div>

	{* Profile removal modal *}

</div>
