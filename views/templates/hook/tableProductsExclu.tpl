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

<div class="table-block">
    <h4 id="ProductsExcluRe" class="table-block__title table-block__holder">{l s='Excluded products' mod='ebay'}
        <button class="button-refresh button btn btn-md btn-default"><span class="icon-refresh"></span> {l s='Refresh' mod='ebay'}</button>
    </h4>

    {if empty($products)}
        <div class="table-block__message table-block__message_green table-block__holder">
            <div class="table-block__message-holder">
                <p>{l s='No excluded products' mod='ebay'}</p>
            </div>
        </div>
    {else}
        <div class="table-wrapper">
            <table id="ProductsExclu" class="table" >
                <thead>
                <tr >
                    <th><span>{l s='ID' mod='ebay'}</span></th>
                    <th><span>{l s='Nom' mod='ebay'}</span></th>
                    <th><span>{l s='Category PrestaShop' mod='ebay'}</span></th>
                    <th><span>{l s='Category Ebay' mod='ebay'}</span></th>
                    <th class="text-center">{l s='Actions' mod='ebay'}</th>
                </tr>
                </thead>

                <tbody>
                {if isset($products)}
                    {foreach from=$products item="product"}
                        <tr>
                            <td>{$product.id_product|escape:'htmlall':'UTF-8'}</td>
                            <td>{$product.name|escape:'htmlall':'UTF-8'}</td>
                            <td>{$product.category_ps|escape:'htmlall':'UTF-8'}</td>
                            <td> {$product.category_ebay|escape:'htmlall':'UTF-8'}</td>
                            <td class="text-center">
                                <a href="#" id="{$product.id_product}" class="btn btn-sm btn-default" name="incluProduct"
                                   title="{l s='Include' mod='ebay'}" data-toggle="tooltip">
                                    <span class="icon-rotate-left"></span>
                                </a>
                            </td>
                        </tr>
                    {/foreach}
                {/if}
                </tbody>
            </table>
        </div>
    {/if}
</div>

{literal}
<script>
    var id_employee = {/literal}{$id_employee|escape:'htmlall':'UTF-8'}{literal};
    $('a[name="incluProduct"]').click(function (e) {
        var tr = $(this).parent().parent();
        e.preventDefault();
        if (confirm(header_ebay_l['Are you sure you want to inclure this product?'])) {
        $.ajax({
            type: 'POST',
            url: ebayProductExcluController,
            data: "ajax=true&action=InclureProduct&id_ebay_profile={/literal}{$id_ebay_profile}{literal}&id_employee=" + id_employee + "&id_product="+$(this).attr('id'),
            success: function (data) {
                //tr.remove();
                window.location.href = url_tab;
            }
        });
        }
    });

    $("#ProductsExcluRe button").click(loadProductExclu);

    {/literal}
</script>



{* Bootstrap tooltip *}
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
