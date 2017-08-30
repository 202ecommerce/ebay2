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


	<button  id="refresh_list" class="btn btn-default">
		{l s='Refresh list' mod='ebay'}
	</button>
	<button class="btn btn-default" id="searchBtn">{l s='Search' mod='ebay'}</button>
    <table class="table" cellpadding="0" cellspacing="0">
    	<tr>
    		<th>{l s='Id product' mod='ebay'}</th>
    		<th>{l s='Quantity' mod='ebay'}</th>
			<th>{l s='Category' mod='ebay'}</th>
    		<th>{l s='Product on Prestashop' mod='ebay'}</th>
    		<th>{l s='Product on eBay (reference)' mod='ebay'}</th>
    	</tr>
		<tr>
			<td>
				<input type="text" class="form-control" id="id_prod_search" value="{$search.id_product}">
			</td>
			<td></td>
			<td>
				<input type="text" class="form-control" id="name_cat_search" value="{$search.name_cat}">
			</td>
			<td>
				<input type="text" class="form-control" id="name_prod_search"  value="{$search.name_product}">
			</td>
			<td>
				<input type="text" class="form-control" id="id_prod_ebay_search" value="{$search.id_product_ebay}">
			</td>
		</tr>
{if $products_ebay_listings}
    		{foreach from=$products_ebay_listings item=product name=loop}
    			<tr class="row_hover{if $smarty.foreach.loop.index % 2} alt_row{/if}">
    				<td style="text-align:center">{$product.id_product|escape:'htmlall':'UTF-8'}</td>
    				<td style="text-align:center">{$product.quantity|escape:'htmlall':'UTF-8'}</td>
					<td style="text-align:center">{$product.category|escape:'htmlall':'UTF-8'}</td>
    				<td><a href="{$product.link|escape:'htmlall':'UTF-8'}" target="_blank">{$product.prestashop_title|escape:'htmlall':'UTF-8'}</a></td>
    				<td><a href="{$product.link_ebay|escape:'htmlall':'UTF-8'}"  target="_blank">{$product.ebay_title|escape:'htmlall':'UTF-8'} ({$product.reference_ebay|escape:'htmlall':'UTF-8'})</a></td>
    			</tr>
    		{/foreach}
{/if}
    </table>

	{if $pages_all >1}
		<div class="navPaginationListProductTab" style="display:flex; justify-content:center">
            {include file=$tpl_include}
		</div>
	{/if}




<script>
	$('#refresh_list').click(refreshList);

    $('#searchBtn').click(search);

    function refreshList(){
        $.ajax({
            type: "POST",
            url: module_dir+'ebay/ajax/getEbayListings.php',
            data: "token="+ebay_token+"&id_employee={$id_employee|escape:'htmlall':'UTF-8'}&id_shop="+id_shop+"&admin_path={$admin_path|escape:'htmlall':'UTF-8'}",
            success: function(data)
            {
                $('#ebayListings').fadeOut(400, function(){
                    $(this).html(data).fadeIn();
                })
            }
        });
	}

	function search() {
        var id_prod;
        var id_prod_ebay;
        var name_cat;
        var name_prod;

        id_prod = $('#id_prod_search').attr('value');
        id_prod_ebay = $('#id_prod_ebay_search').attr('value');
        name_cat = $('#name_cat_search').attr('value');
        name_prod = $('#name_prod_search').attr('value');

        var data = {
            token: ebay_token,
            id_employee: {$id_employee|escape:'htmlall':'UTF-8'},
            id_shop: id_shop,
            admin_path: "{$admin_path|escape:'htmlall':'UTF-8'}",
            page: 1,
            id_prod: id_prod,
            id_prod_ebay: id_prod_ebay,
            name_cat: name_cat,
            name_prod: name_prod,
        }

        $.ajax({
            type: "POST",
            url: module_dir+'ebay/ajax/getEbayListings.php',
            data: data,
            success: function(data)
            {
                $('#ebayListings').fadeOut(400, function(){
                    $(this).html(data).fadeIn();
                })
            }
        });
    }


</script>