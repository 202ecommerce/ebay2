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


<div class="table-block">
    <h4 class="table-block__title table-block__holder">{l s='Ebay listings' mod='ebay'}
        <button class="button-refresh btn btn-default" id="refresh_list_productEbay"><span class="icon-refresh"></span> {l s='Refresh' mod='ebay'}</button>
    </h4>


        <div class="table-block__search table-block__holder">
            <input type="text" class="form-control" id="id_prod_search" value="{$search.id_product}"
                   placeholder="{l s='by ID product' mod='ebay'}"
                   title="{l s='by ID product' mod='ebay'}" data-toggle="tooltip">
            <input type="text" class="form-control" id="name_cat_search" value="{$search.name_cat}"
                   placeholder="{l s='by Category' mod='ebay'}"
                   title="{l s='by Category' mod='ebay'}" data-toggle="tooltip">
            <input type="text" class="form-control" id="name_prod_search"  value="{$search.name_product}"
                   placeholder="{l s='by Product on Prestashop' mod='ebay'}"
                   title="{l s='by Product on Prestashop' mod='ebay'}" data-toggle="tooltip">
            <input type="text" class="form-control" id="id_prod_ebay_search" value="{$search.id_product_ebay}"
                   placeholder="{l s='by Product on eBay (reference)' mod='ebay'}"
                   title="{l s='by Product on eBay (reference)' mod='ebay'}" data-toggle="tooltip">
            <button  id="searchBtnListing" class="button-apply btn btn-info"><span class="icon-search"></span> {l s='Apply' mod='ebay'}</button>
            <button class="button-reset btn btn-default" id="reset_list_productEbay"><span class="icon-close"></span> {l s='Reset' mod='ebay'}</button>
        </div>
    {if empty($products_ebay_listings)}
        <div class="table-block__message table-block__holder">
            <div class="table-block__message-holder">
                <p>{l s='No ebay listings' mod='ebay'}</p>
                <p>{l s='To send products on eBay, please configure "Product synch" tab.' mod='ebay'}</p>
            </div>
        </div>
    {else}
        <div class="table-wrapper">
            <table class="table" cellpadding="0" cellspacing="0">
                <tr>
                    <th>{l s='Id product' mod='ebay'}</th>
                    <th>{l s='Quantity' mod='ebay'}</th>
                    <th>{l s='Category' mod='ebay'}</th>
                    <th>{l s='Product on Prestashop' mod='ebay'}</th>
                    <th>{l s='Product on eBay (reference)' mod='ebay'}</th>
                </tr>
                {if $products_ebay_listings}
                    {foreach from=$products_ebay_listings item=product name=loop}
                        <tr class="row_hover{if $smarty.foreach.loop.index % 2} alt_row{/if}">
                            <td>{$product.id_product|escape:'htmlall':'UTF-8'}</td>
                            <td>{$product.quantity|escape:'htmlall':'UTF-8'}</td>
                            <td>{$product.category|escape:'htmlall':'UTF-8'}</td>
                            <td><a href="{$product.link|escape:'htmlall':'UTF-8'}" target="_blank">{$product.prestashop_title|escape:'htmlall':'UTF-8'}</a></td>
                            <td><a href="{$product.link_ebay|escape:'htmlall':'UTF-8'}"  target="_blank">{$product.ebay_title|escape:'htmlall':'UTF-8'} ({$product.reference_ebay|escape:'htmlall':'UTF-8'})</a></td>
                        </tr>
                    {/foreach}
                {/if}
            </table>
        </div>

        {if $pages_all >1}
            <div class="navPaginationListProductTab" style="display:flex; justify-content:center">
                {include file=$tpl_include}
            </div>
        {/if}
    {/if}
</div>



<script>
    $('#refresh_list_productEbay').click(refreshList);
    $('#reset_list_productEbay').click(refreshList);
    $('#searchBtnListing').click(search);

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
        };

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



{* Bootstrap tooltip *}
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>