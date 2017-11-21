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
	#button_ebay_sync1 {
		background-image: url({/literal}{$path}{literal}views/img/ebay.png);
		background-repeat: no-repeat;
		background-position: center 90px;
		width: 500px;
		height: 191px;
		cursor: pointer;
		padding-bottom: 100px;
		font-weight: bold;
		font-size: 25px;
	}

	#button_ebay_sync2{
		background-image: url({/literal}{$path}{literal}views/img/ebay.png);
		background-repeat: no-repeat;
		background-position: center 90px;
		width: 500px;
		height: 191px;
		cursor: pointer;
		padding-bottom: 100px;
		font-weight: bold;
		font-size: 15px;
	}

	.informations {
		padding-bottom: 3px;margin-top: 8px;
	}

	#nbproducttosync {
		font-weight: bold;
	}

	{/literal}
</style>
<script>
	var product_sync_for_delete;
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
		'message_multi_1'    				: "{l s='This item specific is multi-value on eBay, limited to ' mod='ebay'}",
		'message_multi_2'    				: "{l s=' values. In PrestaShop attribute value, you can use , (coma) and ; (semicolon) to seperate values. For example : \'value 1 , value 2 , value 3 \'' mod='ebay'}",
		{rdelim};


	var conditions_data = new Array();
	{foreach from=$conditions key=type item=condition}
	conditions_data[{$type|escape:'htmlall':'UTF-8'}] = "{$condition|escape:'htmlall':'UTF-8'}";
	{/foreach}

	var possible_attributes = new Array();
	{foreach from=$possible_attributes item=attribute}
	{if isset($attribute.id_attribute_group) && $attribute.id_attribute_group != ""}
	possible_attributes[{$attribute.id_attribute_group|escape:'htmlall':'UTF-8'}] = "{$attribute.name|escape:'htmlall':'UTF-8'}";
	{/if}
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

		$('.js-popin').on('click', function() {

			$('.js-popin').fancybox({
				'modal': true,
				'showCloseButton': false,
				'padding': 0,
				'parent': '#popin-container',
			});
			$('.product_sync_info').hide();
			$('.select_category_default').clone().appendTo($('div .category_ebay'));
			if($('ul.category_ps_list li').length == 0) {
				$('.js-next-popin').attr('disabled','disabled');
			}
		});

		$('.reset_bp').on('click', function() {
			var url = module_dir + "ebay/ajax/resetBp.php?token=" + ebay_token + "&profile=" + id_ebay_profile ;
			$('.bp_group').html("");
			$.ajax({
				type: "POST",
				url: url,
				success: function (data) {
					$('.bp_group').html(data);
				}
			});
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
				$('#last_page_categorie_ps').html('').append($('.category_ps_list').children().clone());
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

			var new_id =  parseInt(courant_page.attr('id'))-1;
			$('.page_popin').html(''+new_id);

			if(new_id == 1){
				$('.js-prev-popin').hide();
			}
			$('.js-save-popin').hide();

			courant_page.parent().find('div#'+new_id).addClass('selected').show();

		});

		$('.js-save-category').on('click', function() {
			var data = $('form#category_config').serialize();
			var ps_categories = new Array();
			$('#last_page_categorie_ps').find('li').each(function( index ) {
				ps_categories.push($( this ).attr('id'));
			});
			event.preventDefault();

			var url = module_dir + "ebay/ajax/saveConfigFormCategory.php";

				$.ajax({
					type: "POST",
					url: url,
					data: "token=" + ebay_token + "&id_employee=" + id_employee + "&id_lang=" + id_lang + "&profile=" + id_ebay_profile + '&id_shop=' + id_shop +'&ps_categories='+ps_categories+ '&'+data,
                    beforeSend:function(){
                        $('.ajaxLoading').show();
					},
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
		    $('#popin-delete-productSync').show();
            product_sync_for_delete = $(this);
            $name_categorie = $(this).closest('tr').children().first().html();
            $('#popin-delete-productSync span.name_categorie').html($name_categorie);
		});

        $('#popin-delete-productSync .cancel-delete').click(function(){
            $('#popin-delete-productSync').hide();
        });

        $('#popin-delete-productSync .ok-delete').click(function(){
            $('#popin-delete-productSync').hide();
            var tr = $(product_sync_for_delete).closest('tr');
            var id_category = $(product_sync_for_delete).data('id');
            $.ajax({
                    cache: false,
                    dataType: 'json',
                    type: "POST",
                    url: module_dir + "ebay/ajax/deleteConfigCategory.php?token=" + ebay_token + "&profile=" + id_ebay_profile + "&ebay_category=" + id_category,
                    success: function (data) {
                        tr.remove();
                    }
                });
        });

		$('.add_categories_ps li').live('click', function() {
			$('.category_ps_list').append($( this ));
            $('.category_ps_list').children().last().children('button').remove();
			$('.category_ps_list').children().last().append('<button type="button" class="js-remove-item  btn btn-xs btn-danger pull-right" title="{/literal}{l s='remove category' mod='ebay'}{literal}"><i class="icon-trash"></i></button>');
			$('#divPsCategories').html('');
			$('.js-next-popin').removeAttr('disabled');
		});


		function loadCategoriesConfig(id_category) {
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
					if (data.percent.type == '%') {
						data.percent.type = "percent";
					}
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

		function loadCategoryItemsSpecifics(category_id) {

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

		showOptionals = function showOptionals(category_id) {
			var nb_rows_to_add = $('tr.optional[category=' + category_id + ']').length;

			var first_td = $('#specifics-' + category_id + ' td:nth-child(1)');
			first_td.attr('rowspan', parseInt(first_td.attr('rowspan')) + nb_rows_to_add - 1);

			$('tr[category=' + category_id + ']').show();
			$('#switch-optionals-' + category_id).hide();

			return false;
		}
		function insertCategoryRow(category_id, data) {
			var has_optionals = false;
			var trs = '';
			var trs_optionals = '';

			// specifics
			var specifics = data.specifics;

			for (var i in specifics)
			{
				var specific = specifics[i];
				var count_specific_values;
				if (specific.max_values && +specific.max_values > 1){
					count_specific_values = '<label class="control-label"><span class ="label-tooltip" data-toggle="tooltip" title="'+l['message_multi_1'] + specific.max_values + l['message_multi_2'] +'"><i class ="icon-list-ul"></i></span></label>';
				} else{
                    count_specific_values = '';
				}
				var tds = '<td>' + specific.name + ' ' + count_specific_values + '</td><td>';
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
			$('[data-toggle="tooltip"]').tooltip({placement:'right'});
		}

		function writeOptions(value_prefix, options, selected_id) {
			var str = '';

			for (var id in options)
				str += '<option value="' + value_prefix + '-' + id + '" ' + (id == selected_id ? 'selected' : '') + '>' + options[id] + '</option>';

			return str;
		}

		function showProducts(id_category) {
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

	function changeCategoryMatch(level, id_categoris) {
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

	function loadPsCategories(search) {
		var url = module_dir + "ebay/ajax/loadAjaxCategories.php?token=" + ebay_token + "&id_lang=" + id_lang + "&profile=" + id_ebay_profile + '&id_shop=' + id_shop  +'&s=' + search;
		var selected_cat = new Array();
		$('.category_ps_list li').each(function(){
		    selected_cat.push($(this).text());
		});

		$.ajax({
			type: "POST",
			url: url,
			success: function (data) {
				var data = jQuery.parseJSON(data);
				var str = '<ul class="add_categories_ps">';
				$.each(data.categoryList, function( index, value ) {
				    if (selected_cat.indexOf(value.name) == -1){
                        str += '<li id="'+ value.id_category +'">'+ value.name +'</li>';
					}
				});
				str += '</ul>';
				$('#divPsCategories').html('').append(str);
			}
		});

	}

	{/literal}
</script>

<div id="resultSync" style="text-align: center; font-weight: bold; font-size: 14px;"></div>

<fieldset class="table-block-below">
  {if isset($img_alert) && !empty($img_alert)}
    <div class="warning big">
      {$img_alert['message']|escape:'htmlall':'UTF-8'}
      {if isset($img_alert.kb)}
        <a class="kb-help" data-errorcode="{$img_alert.kb.errorcode}" data-module="ebay"
           data-lang="{$img_alert.kb.lang}" module_version="{$img_alert.kb.module_version}"
           prestashop_version="{$img_alert.kb.prestashop_version}">&nbsp;<i class="icon-info-circle"></i></a>
      {/if}
    </div>
  {/if}
  {if isset($category_alerts) && !empty($category_alerts)}
    <div class="warning big">
      {$category_alerts|escape:'htmlall':'UTF-8'}
    </div>
  {/if}
</fieldset>
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
<div class="table-block">
	<h4 class="table-block__title table-block__holder">{l s='Prestashop categories' mod='ebay'}
    <div>
      <a href="#popin-add-cat" class="js-popin btn btn-success" {if $shipping_tab_is_conf}disabled="disabled"{/if}><span class="icon-plus"></span> {l s='Add' mod='ebay'} </a>
      <div class="dropdown js-user-dropdown button-sync">
        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
          <span class="icon-refresh"></span> {l s='Manual sync' mod='ebay'} <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
          <li data-action="resyncProductsAndImages">{l s='Synchronize all products sending new images' mod='ebay'}</li>
          <li data-action="resyncProducts">{l s='Synchronize all products' mod='ebay'}</li>
        </ul>
      </div>
    </div>
	</h4>

  <div id="searcheEbaySync" class="table-block__search table-block__holder">
      <input type="text" class="name_cat" placeholder="{l s='by category name' mod='ebay'}" {if $searche}value="{$searche}"{/if}
             title="{l s='by category name' mod='ebay'}" data-toggle="tooltip">
      <input type="text" class="id_prod" placeholder="{l s='by ID product' mod='ebay'}"
             title="{l s='by ID product' mod='ebay'}" data-toggle="tooltip"
             {if isset($filter.id_product)}value="{$filter.id_product}"{/if}>
      <input type="text" class="name_prod" placeholder="{l s='by product name' mod='ebay'}"
             title="{l s='by product name' mod='ebay'}" data-toggle="tooltip"
             {if isset($filter.name_product)}value="{$filter.name_product}"{/if}>
      <button class="searcheBtn button-apply btn btn-info"><span class="icon-search"></span> {l s='Apply' mod='ebay'}</button>
      <button class="researcheBtn button-reset btn btn-default"><span class="icon-close"></span> {l s='Reset' mod='ebay'}</button>
  </div>
	{if $categories|@count == 0}
		<div class="table-block__message table-block__holder">
			<div class="table-block__message-holder">
				<p>{l s='No list categories' mod='ebay'}</p>
				<p>{l s='To send categories on eBay, click on button "+ Add".' mod='ebay'}</p>
			</div>
		</div>
	{else}
        <div id="catSync" class="table-wrapper">
            <table class="table tableDnD" cellpadding="0" cellspacing="0" width="90%">
                <thead>
                    <tr class="nodrag nodrop">
                        <th>{l s='PrestaShop Category' mod='ebay'}</th>
                        <th>{l s='Products / variations' mod='ebay'}</th>
                        <th>{l s='Manual Exclusions' mod='ebay'}</th>
                        <th>{l s='Price impact' mod='ebay'}</th>
                        <th>{l s='EBay category' mod='ebay'}</th>
                        <th>{l s='Multi var' mod='ebay'}</th>
                        <th>{l s='Listing' mod='ebay'}</th>
                        <th class="text-center">{l s='Status' mod='ebay'}</th>
                        <th class="text-center">{l s='Action' mod='ebay'}</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$categories item=category}
                    <tr class="{$category.row_class|escape:'htmlall':'UTF-8'}">
                        <td>{$category.name|escape:'htmlall':'UTF-8'}</td>
                        <td>{if $category.category_multi == 'yes'}<b>{$category.nb_products|escape:'htmlall':'UTF-8'}</b>/{$category.nb_products_variations|escape:'htmlall':'UTF-8'}{else}{$category.nb_products|escape:'htmlall':'UTF-8'}/<b>{$category.nb_products_variations|escape:'htmlall':'UTF-8'}</b>{/if} </td>
                        <td>{$category.nb_products_blocked|escape:'htmlall':'UTF-8'}</td>
                        <td>{if $category.price}{$category.price|escape:'htmlall':'UTF-8'}{else}0{/if}</td>
                        <td>{$category.category_ebay|escape:'htmlall':'UTF-8'}</td>
                        <td>{$category.category_multi|escape:'htmlall':'UTF-8'}</td>
                        <td>{$category.annonces|escape:'htmlall':'UTF-8'}/{if $category.category_multi == 'yes'}{$category.nb_product_tosync|escape:'htmlall':'UTF-8'}{else}{$category.nb_variations_tosync|escape:'htmlall':'UTF-8'}{/if}</td>
                        <td class="text-center"><input type="checkbox" class="categorySync" id="categorySync{$category.value|escape:'htmlall':'UTF-8'}" name="category[]" value="{$category.value|escape:'htmlall':'UTF-8'}" {$category.checked|escape:'htmlall':'UTF-8'} />
                            <label for="categorySync{$category.value|escape:'htmlall':'UTF-8'}" class="btn btn-default btn-sm"  title="{l s='Activate/Deactivate' mod='ebay'}" data-toggle="tooltip"></label>
                        </td>
                        <td>
                            <div class="action">
                                <a href="#popin-add-cat" class="modifier_cat btn btn-sm btn-default" data-id="{$category.value}" title="{l s='Edit' mod='ebay'}" data-toggle="tooltip"><span ></span><span class="icon-pencil"></span></a>
                                <a href="#" class="delete_cat btn-hover-danger  btn btn-sm btn-default" data-id="{$category.value}" title="{l s='Remove' mod='ebay'}" data-toggle="tooltip"><span ><span class="icon-trash"></span></a>
                            </div>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>

		{if isset($pagination) && $pagination}
			<div class="navPaginationSyncTab" style="display:flex; justify-content:center">
				{include file=$tpl_include}
			</div>
		{/if}

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
    {/if}
</div>

{* Category add modal *}
<div id="popin-add-cat" class="popin popin-lg" style="display: none;">
	<div class="panel">
		<div  class="panel-heading">
			<i class="icon-plus"></i> {l s='ADD A CATEGORY' mod='ebay'}
			<span class="badge badge-success"><span class="page_popin" id="1">1</span> / 4</span>
		</div>

			<form action="" class="form-horizontal" method="post" id="category_config">
				<div id="1" class="page_config_category selected first_page_popin">
					<div class="form-group page">
						<label for="" class="control-label col-md-6" data-toggle="tooltip" data-placement="bottom" title="Pour des raisons de performances, il est recommandé de se limiter à 3 catégories par annonce.">
							{l s='Select one or more PrestaShop category :' mod='ebay'}
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
							<span>{l s='Products :' mod='ebay'} <span class="badge badge-info" id="form_product_to_sync"></span></span>
							<span>{l s='Variations :' mod='ebay'} <span class="badge badge-info" id="form_variations_to_sync"></span></span>
						</div>
					</div>

					<div class="form-group">
						<label for="" class="control-label col-md-6">
							{l s='Select a eBay category :' mod='ebay'}
						</label>
						<div class="input-group col-md-6 category_ebay">


						</div>
						<input type="hidden" name="category[0]" value="0">
					</div>

					<div class="form-group">
						<label for="" class="control-label col-md-6">
							{l s='Impact on price :' mod='ebay'}
						</label>
						<div class="input-group col-md-6">
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

				<div class="form-group">
					<label for="" class="control-label col-md-6">
						{l s='Select a category of your eBay shop :' mod='ebay'}
					</label>
					{if $storeCategories}
					<div class="input-group col-md-6">
						<select name="store_category">
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
						{l s='Return Policies* :' mod='ebay'}
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
							{l s='Payement  Policies* :' mod='ebay'}
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
						<a href="#" class="reset_bp btn btn-lg btn-success"><span></span> {l s='Reset BP' mod='ebay'}</a>

					{/if}

				</div>
				<div id="2" class="page_config_category" style="display: none">
					<div class="form-group">
						<label for="" class="control-label col-md-6">
							{l s='Match the PrestaShop characteristics :' mod='ebay'}
						</label>
						<div class="input-group col-md-12 category_spec_ebay">
							<table class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%;">
								<thead>
								<tr class="nodrag nodrop">
									<th style="width:20%">
										<span data-inlinehelp="Les premieres caractéristiques de produits sont obligatoires, et vous ne pourrez pas exporter vos produits sans les ajouter. Vous pouvez aussi ajouter des caractéristiques optionneles qui aideront l'acheteur à trouver vos objets. Dans le deuxième encart, renseignez l'état de vos objets">{l s='Item specifics' mod='ebay'}</span><a class=" tooltip" target="_blank"> <img src="../img/admin/help.png" alt=""></a>
									</th>
									<th style="width:50%">
										{l s='Prestashop characteristics' mod='ebay'}
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
							{l s='Choose products to exclude from eBay :' mod='ebay'}
						</label>
						<div class="input-group col-md-12 category_product_list_ebay">
							<table class="table tableDnD" width="80%" style="margin: auto">
								<thead>
								<tr class="product-row" category="5">
									<th class="">{l s='ID Product' mod='ebay'}</th>
									<th class="">{l s='Name' mod='ebay'}</th>
									<th class="ebay_center ">{l s='Stock' mod='ebay'}</th>
									<th class="ebay_center ">{l s='Deselect products you do not want to sell on eBay' mod='ebay'}
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
							{l s='Confirm configuration' mod='ebay'}
						</label>
					</div>
					<div class="input-group col-md-12 total_config_list_ebay">
						<div class="col-md-12">
							<br>
							 <b>{l s='PrestaShop categories :' mod='ebay'} </b><span id="last_page_categorie_ps"> </span><br>
							 <b>{l s='EBay Category :' mod='ebay'} </b><span id="last_page_categorie_ebay"></span><br>
							 <b>{l s='Impact price :' mod='ebay'} </b><span id="last_page_categorie_prix"> </span><br>
							 <b>{l s='Category of your eBay Store :' mod='ebay'} </b><span id="last_page_categorie_boutique"></span><br>
							<br>
							{if $bp_active}
								<h4>{l s='Business Policies' mod='ebay'}</h4>
								 <b>{l s='Payement Policies :' mod='ebay'} </b><span id="last_page_business_p"></span><br>
							 	<b>{l s='Return Policies :' mod='ebay'} </b><span id="last_page_business_r"> </span><br><br><br>
							{/if}
							{l s='The addition of these products is done automatically in the next few minutes.' mod='ebay'}
							<br>
							<br>
							<a href="#popin-categorie-save" class="js-save1-popin btn btn-lg btn-success pull-right">{l s='Save' mod='ebay'}</a>

						</div>
					</div>

					<div id="popin-categorie-save" class="popin popin-sm" style="display: none;position: fixed;z-index: 1;left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgb(0,0,0);background-color: rgba(0,0,0,0.4);">
						<div class="panel" style=" background-color: #fefefe;padding: 20px;border: 1px solid #888;width: 80%;margin: 30% auto;">
							<div class="panel-heading">
								<i class="icon-save"></i> {l s='Save Categories' mod='ebay'}
							</div>
							<p>{l s='This action will add/modify ' mod='ebay'} <span class="nb_annonces"></span> {l s='listings on eBay.' mod='ebay'}</p>

							<div class="panel-footer">
								<button class="js-notsave btn btn-default"><i class="process-icon-cancel"></i>{l s='Cancel' mod='ebay'}</button>
								<button class="js-save-category btn btn-success pull-right"><i class="process-icon-save"></i>OK</button>
								<div class="ajaxLoading pull-right" style="display:none; margin-top:10px"><img src="../modules/ebay/views/img/ajax-loader-small.gif"></div>
							</div>
						</div>
					</div>

				</div>
			</form>

			<div class="panel-footer">
				<button class="js-close-popin btn btn-default"><i class="process-icon-cancel"></i>{l s='Cancel' mod='ebay'}</button>
				<button class="js-next-popin btn btn-primary pull-right"><i class="process-icon-next"></i>{l s='Next' mod='ebay'}</button>

				<button class="js-prev-popin btn btn-primary pull-right" style="display: none"><i class="process-icon-next"  style="transform: rotateZ(180deg);transform-origin: 50% 45%;"></i>{l s='Prev' mod='ebay'}</button>
			</div>
			<div style="display: none">
				<select class="select_category_default" name="category" id="categoryLevel1-0" rel="0" style="font-size: 12px;" onchange="changeCategoryMatch(1,0);">
					<option value="0">no category selected</option>
					{foreach from=$ebayCategories item=ebayCategory}
						<option value="{$ebayCategory.id_ebay_category}" >{$ebayCategory.name}</option>
					{/foreach}
				</select>
		</div>
	</div>
{* Profile removal modal *}
</div>
<div id="popin-delete-productSync" class="popin popin-sm" style="display: none;position: fixed;z-index: 1;left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgb(0,0,0);background-color: rgba(0,0,0,0.4);">
	<div class="panel" style=" background-color: #fefefe;padding: 20px;border: 1px solid #888;width: 80%;margin: 30% auto;">
		<div class="panel-heading">
			<i class="icon-trash"></i> {l s='Synchronization product' mod='ebay'}
		</div>
		<p>{l s='Do you want to stop and delete the synchronization of the category' mod='ebay'} <span class="name_categorie"></span>?</p>
		<p>{l s='Then you can find the eBay products of this category in the orphan listing, where you can delete them definitevely.' mod='ebay'}</p>

		<div class="panel-footer" style="display: flex; justify-content: space-between; align-items: center">
			<button class="cancel-delete btn btn-default"><i class="process-icon-cancel"></i>Annuler</button>
			<button class="ok-delete btn btn-success pull-right" style="padding:15px">OK</button>
		</div>
	</div>
</div>

<div id="popin-cofirm-resync" class="popin popin-sm" style="display: none;position: fixed;z-index: 1;left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgb(0,0,0);background-color: rgba(0,0,0,0.4);">
	<div class="panel" style=" background-color: #fefefe;padding: 20px;border: 1px solid #888;width: 80%;margin: 30% auto;">
		<div class="panel-heading">
			<i class="icon-trash"></i> {l s='Synchronization product' mod='ebay'}
		</div>
		<input hidden data-role="modeResync">
		<p>{l s='Do you want resync the products?' mod='ebay'}</p>

		<div class="panel-footer" style="display: flex; justify-content: space-between; align-items: center">
			<button class="cancel-resyncProducts btn btn-default"><i class="process-icon-cancel"></i>Annuler</button>
			<button class="ok-resyncProducts btn btn-success pull-right" style="padding:15px">OK</button>
		</div>
	</div>
</div>

{* Bootstrap tooltip *}
<script>
	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();
	});
</script>
