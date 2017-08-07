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


        <p>{l s='The following products has been refused from eBay. Pay attention: if an listing is already on eBay, it will be no more upgraded. You will need to manually correct the eBay refusal, or you can delete the product from your synchronization.' mod='ebay'}</p>
        <div id = 'contentProductErrors'>
            <table id="AnnoncesErrorsListings" class="table" >
                <thead>
                <tr>

                    <th style="width:110px;">
                        <span>{l s='Date' mod='ebay'}</span>
                    </th>

                    <th>
                        <span>{l s='ID' mod='ebay'}</span>
                    </th>

                    <th class="center">
                        <span>{l s='Nom' mod='ebay'}</span>
                    </th>

                    <th>
                        <span >{l s='eBay listings' mod='ebay'}</span>
                    </th>

                    <th class="center">
                        <span >{l s='Error' mod='ebay'}</span>
                    </th>

                    <th class="center">
                        <span >{l s='Description' mod='ebay'}</span>
                    </th>

                    <th class="center">
                        <span>{l s='Stock' mod='ebay'}</span>
                    </th>

                    <th class="center">
                        <span>{l s='Help' mod='ebay'}</span>
                    </th>

                    <th class="center">{l s='Actions' mod='ebay'}</th>

                </tr>
                </thead>

                <tbody>
                {if isset($task_errors)}
                    {foreach from=$task_errors item="task_error"}
                        <tr>
                            <td>{$task_error.date|escape:'htmlall':'UTF-8'}</td>
                            <td>{$task_error.id_product|escape:'htmlall':'UTF-8'}</td>
                            <td>{$task_error.name|escape:'htmlall':'UTF-8'}</td>
                            <td class="item_id_error">{$task_error.id_item|escape:'htmlall':'UTF-8'}</td>
                            <td>{$task_error.error|escape:'htmlall':'UTF-8'}</td>
                            <td class="error_description">{$task_error.desc_error|escape:'htmlall':'UTF-8'}</td>
                            <td ></td>
                            <td ><a class="kb-help"
                                    data-errorcode="{$task_error.error_code}"
                                    data-module="ebay"
                                    data-lang="{$task_error.lang_iso}"
                                    module_version="1.11.0"
                                    prestashop_version="{$task_error.ps_version}"></a></td>
                            <td ><a class="btn btn-xs btn-block btn-warning corige_product" id="{$task_error.real_id}" href="{$task_error.product_url}" target="_blank"><i class="icon-gavel"></i>{l s='Corriger' mod='ebay'}</a>
                                <a class="btn btn-xs btn-block btn-danger exclure_product" id="{$task_error.real_id}"><i class="icon-ban"></i>{l s='Exclure' mod='ebay'}</a></td>
                        </tr>
                    {/foreach}
                {/if}
                </tbody>

            </table>
            {if $pages_all >1}
                <div class="navPaginationListProducErrorsTab" style="display:flex; justify-content:center">
                    {include file=$tpl_include}
                </div>
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
    $(document).on('click', '#popin-product-corige', function(){
        $(this).hide();
    });

    $(document).on('click', '#popin-product-corige .panel', function(e){
        e.stopPropagation();
    });

    $(document).on('click', '.exclure_product', function (e) {
        e.preventDefault();
        $('.if_anonnces_exist').attr('id', $(this).attr('id'));
        if($(this).parent().parent().find('.item_id_error').html()){
            $('p.if_anonnces_exist').show();
        }
        $('#popin-product-exclu').show();

    });
    $(document).on('click', '.corige_product', function (e) {
        e.preventDefault();
        $('.url_product_info').attr('href', $(this).attr('href'));
        $('.error_product').html($(this).parent().parent().find('.error_description').text());
        $('#popin-product-corige').show();

    });
    $(document).on('click', '.js-exclu-confirm', function (e) {
        e.preventDefault();
        exclureProduct($('.if_anonnces_exist').attr('id'));
        $('#popin-product-exclu').hide();
        $('.if_anonnces_exist').hide();
        location.reload();
    });
    $(document).on('click', '.js-notexclu', function (e) {
        e.preventDefault();
        $('#popin-product-exclu').hide();
        $('.if_anonnces_exist').hide();
    });
    $(document).on('click', '.js-corige-ok', function (e) {
        e.preventDefault();
        $('#popin-product-corige').hide();
    });
    function exclureProduct(id_product) {
        $.ajax({
            type: 'POST',
            url: module_dir + 'ebay/ajax/exclureProductAjax.php',
            data: "token={/literal}{$ebay_token}{literal}&id_ebay_profile={/literal}{$id_ebay_profile}{literal}&id_product="+id_product,
            success: function (data) {
                $(this).parent().parent().remove();
            }
        });
    }

    $(document).on('click', '.navPaginationListProducErrorsTab .pagination span', function(){
        var page = $(this).attr('value');
        if(page){
            $.ajax({
                type: "POST",
                url: module_dir+'ebay/ajax/paginationProductErrors.php',
                data: "token_for_product={/literal}{$token_for_product}{literal}&profile=" + id_ebay_profile + "&page=" + page,
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

    {/literal}
</script>