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


<div class="table-block">
    {*<p>{l s='The following products has been refused from eBay. Pay attention: if an listing is already on eBay, it will be no more upgraded. You will need to manually correct the eBay refusal, or you can delete the product from your synchronization.' mod='ebay'}</p>*}
    <h4 class="table-block__title table-block__holder">{l s='Product errors' mod='ebay'}
        <button class="button-refresh btn btn-default" id="refreshErrors"><span class="icon-refresh"></span> {l s='Refresh' mod='ebay'}</button>
    </h4>


        <div class="table-block__search table-block__holder">
            <input type="text" class="form-control" id="id_product_search"
                   placeholder="{l s='by ID product' mod='ebay'}"
                   title="{l s='by ID product' mod='ebay'}" data-toggle="tooltip"
                   value="{if isset($search.id_product)}{$search.id_product}{/if}">
            <input type="text" class="form-control" id="name_product_search"
                   placeholder="{l s='by product name' mod='ebay'}"
                   title="{l s='by product name' mod='ebay'}" data-toggle="tooltip"
                   value="{if isset($search.name_product)}{$search.name_product}{/if}">
            <button class="button-apply btn btn-info" id="searchBtnErrors"><span class="icon-search"></span> {l s='Apply' mod='ebay'}</button>
            <button class="button-reset btn btn-default" id="resetErrors"><span class="icon-close"></span> {l s='Reset' mod='ebay'}</button>
        </div>
    {if empty($task_errors)}
        <div class="table-block__message table-block__message_green table-block__holder">
            <div class="table-block__message-holder">
                <p>{l s='No errors listings' mod='ebay'}</p>
            </div>
        </div>
    {else}
        <div id="contentProductErrors" class="table-wrapper">
            <table id="AnnoncesErrorsListings" class="table">
                <thead>
                <tr>
                    <th style="width:110px;"><span>{l s='Date' mod='ebay'}</span></th>
                    <th><span>{l s='ID' mod='ebay'}</span></th>
                    <th class="text-center"><span>{l s='Nom' mod='ebay'}</span></th>
                    <th><span >{l s='eBay listings' mod='ebay'}</span></th>
                    <th class="text-center"><span >{l s='Error' mod='ebay'}</span></th>
                    <th class="text-center"><span >{l s='Description' mod='ebay'}</span></th>
                    <th class="text-center"><span>{l s='Stock' mod='ebay'}</span></th>
                    <th class="text-center">{l s='Actions' mod='ebay'}</th>
                </tr>
                </thead>

                <tbody>
                {if isset($task_errors) && !empty($task_errors)}
                    {foreach from=$task_errors item="task_error"}
                        <tr>
                            <td>{$task_error.date|escape:'htmlall':'UTF-8'}</td>
                            <td>{$task_error.id_product|escape:'htmlall':'UTF-8'}</td>
                            <td>{$task_error.name|escape:'htmlall':'UTF-8'}</td>
                            <td class="item_id_error">{$task_error.id_item|escape:'htmlall':'UTF-8'}</td>
                            <td>{$task_error.error|escape:'htmlall':'UTF-8'}</td>
                            <td class="error_description">{$task_error.desc_error}</td>{*cannot be escaped*}
                            <td></td>
                            <td>
                                <div class="action">
                                    {*<a class="btn btn-sm btn-default kb-help"*}
                                       {*data-errorcode="{$task_error.error_code}"*}
                                       {*data-module="ebay"*}
                                       {*data-lang="{$task_error.lang_iso}"*}
                                       {*module_version="1.11.0"*}
                                       {*prestashop_version="{$task_error.ps_version}"*}
                                       {*title="{l s='Help' mod='ebay'}" data-toggle="tooltip"><i class="icon-question"></i>*}
                                    {*</a>*}
                                    <a class="btn btn-sm btn-default corige_product"
                                       id="{$task_error.real_id}" href="{$task_error.product_url}"
                                       target="_blank"
                                       {*title="{l s='Edit' mod='ebay'}" data-toggle="tooltip"><i class="icon-pencil"></i>*}
                                       title="{l s='Help' mod='ebay'}" data-toggle="tooltip"><i class="icon-question"></i>
                                    </a>
                                    <a class="exclure_product btn-hover-danger  btn btn-sm btn-default"
                                       id="{$task_error.real_id}"
                                       title="{l s='Remove' mod='ebay'}" data-toggle="tooltip"><i class="icon-close"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                {/if}
                </tbody>

            </table>
        </div>

        {if isset($pages_all) and $pages_all >1}
            <div class="navPaginationListProducErrorsTab" style="display:flex; justify-content:center">
                {include file=$tpl_include}
            </div>
        {/if}
    {/if}
</div>

<div id="popin-product-exclu" class="popin popin-sm" style="display: none;position: fixed;z-index: 1;left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgb(0,0,0);background-color: rgba(0,0,0,0.4);">
    <div class="panel" style=" background-color: #fefefe;padding: 20px;border: 1px solid #888;width: 80%;margin: 30% auto;">
        <div class="panel-heading">
            <i class="icon-trash"></i> Exclure Product
        </div>
        <p>Si vous ne souhaitez pas corriger cette erreur immédiatement, vous pouvez exclure le produit de la synchronisation.
        </p>

        <p class="if_anonnces_exist" style="display: none;">Attention, une annonce eBay existe déjà. L'exclusion va mettre le stock de l'annonce eBay à 0, ce qui empèchera tout achat.
        </p>
        {if $out_of_stock}
        <p >Attention, vous n’utilisez pas le statut « Hors stock », merci de l’activer
        </p>
        {/if}
        <div class="panel-footer" style="display: flex; justify-content: space-between; align-items: center">
            <button class="js-notexclu btn btn-default"><i class="process-icon-cancel"></i>Annuler</button>
            <button class="js-exclu-confirm btn btn-success pull-right" style="padding:15px">OK</button>
        </div>
    </div>
</div>

<div id="popin-product-corige" class="popin popin-sm" style="display: none;position: fixed;z-index: 3;left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgb(0,0,0);background-color: rgba(0,0,0,0.4);">
    <div class="panel" style=" background-color: #fefefe;padding: 20px;border: 1px solid #888;width: 80%;margin: 12% auto;">
        <div class="panel-heading" style="text-align: center;>
            <i class="icon-trash"></i> Comment corriger ?
        </div>
        <p>Pour corriger, vous devez changer la configuration du module, ou mettre à jour le <a class="url_product_info" href="" target="_blank"> produit PrestaShop</a>, pour répondre au rejet de eBay.
        </p>
        </br>
        <p style="text-align: center;"><span class="error_product" style="font-style: italic;"></span>
        </p>

        </br>
        <p>202 ecommerce vous propose cette aide.</p>
        </br>
        <p>Lorsque vous corrigerez la configuration du module, ou le produit PrestaShop, une nouvelle synchronisation sera lancée automatiquement. Si le problème est corrigé, l’erreur disparaitra de cette liste.
        </p>
        </br>
        <div class="panel-footer" style="text-align: center;">
            <button class="js-corige-ok btn btn-success" style="padding:15px">OK</button>
        </div>
    </div>
</div>

{literal}
<script>
    var ebayListErrorsProductsController = {/literal}"{$ebayListErrorsProductsController|addslashes}";{literal}
    $('#popin-product-corige').click(function(){
        $(this).hide();
    });

    $('#popin-product-corige .panel').click(function(e){
        e.stopPropagation();
    });

    $('.exclure_product').click(function (e) {
        e.preventDefault();
        $('.if_anonnces_exist').attr('id', $(this).attr('id'));
        if($(this).parent().parent().find('.item_id_error').html()){
            $('p.if_anonnces_exist').show();
        }
        $('#popin-product-exclu').show();

    });
    $('.corige_product').click(function (e) {
        e.preventDefault();
        $('.url_product_info').attr('href', $(this).attr('href'));
        $('.error_product').html($(this).parent().parent().find('.error_description').text());
        $('#popin-product-corige').show();

    });
    $('.js-exclu-confirm').click(function (e) {
        e.preventDefault();
        exclureProduct($('.if_anonnces_exist').attr('id'));
        $('#popin-product-exclu').hide();
        $('.if_anonnces_exist').hide();
        location.reload();
    });
    $('.js-notexclu').click(function (e) {
        e.preventDefault();
        $('#popin-product-exclu').hide();
        $('.if_anonnces_exist').hide();
    });
    $('.js-corige-ok').click(function (e) {
        e.preventDefault();
        $('#popin-product-corige').hide();
    });
    function exclureProduct(id_product) {
        $.ajax({
            type: 'POST',
            url: ebayListErrorsProductsController,
            data: "ajax=true&action=ExclureProductAjax&id_ebay_profile={/literal}{if isset($id_ebay_profile)}{$id_ebay_profile}{/if}{literal}&id_product="+id_product,
            success: function (data) {
                $(this).parent().parent().remove();
            }
        });
    }

    $('.navPaginationListProducErrorsTab .pagination span').click(function(){
        var page = $(this).attr('value');
        if(page){
            var id_product;
            var name_product;

            id_product = $('#id_product_search').attr('value');
            name_product = $('#name_product_search').attr('value');

            var data = {
                id_product: id_product,
                name_product: name_product,
                token_for_product: {/literal}{if isset($token_for_product)}"{$token_for_product}"{else}''{/if}{literal},
                profile: id_ebay_profile,
                page: page,
                ajax: true,
                action: 'PaginationProductErrors'
            };

            $.ajax({
                type: "POST",
                url: ebayListErrorsProductsController,
                data: data,
                beforeSend : function(){
                    var html = '<div style=" position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px;"></div>';
                    $('#menuTab80Sheet .panel').empty();
                    $('#menuTab80Sheet .panel').append(html);
                },
                success: function(data)
                {
                    $('#menuTab80Sheet .panel').html(data)
                }
            });
        }
    });


    $('#searchBtnErrors').click(function(){
        var id_product;
        var name_product;

        id_product = $('#id_product_search').attr('value');
        name_product = $('#name_product_search').attr('value');

        var data = {
            id_product: id_product,
            name_product: name_product,
            token_for_product: {/literal}{if isset($token_for_product)}"{$token_for_product}"{else}""{/if}{literal},
            profile: id_ebay_profile,
            page: 1,
            ajax: true,
            action: 'PaginationProductErrors'
        };

        $.ajax({
            type: "POST",
            url: ebayListErrorsProductsController,
            data: data,
            beforeSend : function(){
                var html = '<div style=" position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px;"></div>';
                $('#menuTab80Sheet .panel').empty();
                $('#menuTab80Sheet .panel').append(html);
            },
            success: function(data)
            {
                $('#menuTab80Sheet .panel').html(data)
            }
        });
    });

    $('#refreshErrors').click(refreshErrors);

    $('#resetErrors').click(refreshErrors);

    function refreshErrors(){
        $('#menuTab80Sheet .panel').empty();
        var data = {
            id_ebay_profile : id_ebay_profile,
            token_for_product: {/literal}{if isset($token_for_product)}"{$token_for_product}"{else}""{/if}{literal},
            ajax: true,
            action: 'RefreshErrors',
        };
        $.post(ebayListErrorsProductsController, data, function(response){
            $('#menuTab80Sheet .panel').html(response);
        });
    };

    {/literal}
</script>


{* Bootstrap tooltip *}
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
