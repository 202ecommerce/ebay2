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
<div id="ProductsExcluRe">
    <p class="center">
        <button class="button">{l s='Refresh excluded products' mod='ebay'}</button>
    </p>
</div>
</br>

<table id="ProductsExclu" class="table" >
    <thead>
    <tr >

        <th>
            <span>{l s='ID' mod='ebay'}</span>
        </th>

        <th class="center">
            <span>{l s='Nom' mod='ebay'}</span>
        </th>
        <th class="center">
            <span>{l s='Category PrestaShop' mod='ebay'}</span>
        </th>

        <th class="center">
            <span>{l s='Category Ebay' mod='ebay'}</span>
        </th>

        <th class="center">{l s='Actions' mod='ebay'}</th>

    </tr>
    </thead>

    <tbody>
    {if isset($products)}
        {foreach from=$products item="product"}
            <tr>
                <td class="center">{$product.id_product|escape:'htmlall':'UTF-8'}</td>
                <td class="center">{$product.name|escape:'htmlall':'UTF-8'}</td>
                <td class="center">{$product.category_ps|escape:'htmlall':'UTF-8'}</td>
                <td class="center"> {$product.category_ebay|escape:'htmlall':'UTF-8'}</td>
                <td ><a class="btn btn-xs btn-block btn-warning" name="incluProduct" id="{$product.id_product}" href="#">{l s='Include' mod='ebay'}</a></td>
            </tr>
        {/foreach}
    {/if}
    </tbody>

</table>

{literal}
<script>
    var id_employee = {/literal}{$id_employee|escape:'htmlall':'UTF-8'}{literal};
    $('a[name="incluProduct"]').click(function (e) {
        var tr = $(this).parent().parent();
        e.preventDefault();
        if (confirm(header_ebay_l['Are you sure you want to inclure this product?'])) {
        $.ajax({
            type: 'POST',
            url: module_dir + 'ebay/ajax/inclureProductAjax.php',
            data: "token={/literal}{$ebay_token}{literal}&id_ebay_profile={/literal}{$id_ebay_profile}{literal}&id_employee=" + id_employee + "&id_product="+$(this).attr('id'),
            success: function (data) {
                //tr.remove();
                window.location.href = url_tab;
            }
        });
        }
    });
    var content_ebay_relistings = $("#ProductsExcluRe button");
    content_ebay_relistings.live('click', 'button', function(){
        loadProductExclu();
    });

    {/literal}
</script>