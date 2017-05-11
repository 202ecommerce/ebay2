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
<p>
    <span><b>{l s='Commandes importées par' mod='ebay'} {$type_sync_order}<br>
        {l s='Dernier import :' mod='ebay'} {$date_last_import|escape:'htmlall':'UTF-8'}</b> </span>
</p>

<p>
    <a href="{$url|escape:'htmlall':'UTF-8'}&EBAY_SYNC_ORDERS=1" class="btn btn-default">
        <i class="icon-refresh"></i> <span>{l s='Sync Orders from eBay' mod='ebay'}</span>
    </a>
</p>

    <!-- table -->
    <table id="OrderListings" class="table " >
        <thead>
        <tr >

            <th style="width:110px;">
                <span>{l s='Data eBay' mod='ebay'}</span>
            </th>

            <th>
                <span>{l s='Referance eBay' mod='ebay'}</span>
            </th>

            <th class="center">
                <span>{l s='Referance b' mod='ebay'}</span>
            </th>

            <th class="center">
                <span >{l s='email' mod='ebay'}</span>
            </th>

            <th>
                <span >{l s='Total' mod='ebay'}</span>
            </th>


            <th class="center">
                <span >{l s='ID PrestaShop' mod='ebay'}</span>
            </th>

            <th class="center">
                <span>{l s='PrestaShop referance' mod='ebay'}</span>
            </th>

            <th class="center">
                <span>{l s='Date Import' mod='ebay'}</span>
            </th>

            <th class="center">{l s='Actions' mod='ebay'}</th>

        </tr>
        </thead
        <tbody>
        {if isset($errors)}
        {foreach from=$errors item="error"}
            <tr>
                <td>{$error.date_ebay|escape:'htmlall':'UTF-8'}</td>
                <td>{$error.reference_ebay|escape:'htmlall':'UTF-8'}</td>
                <td>{$error.referance_marchand|escape:'htmlall':'UTF-8'}</td>
                <td>{$error.email|escape:'htmlall':'UTF-8'}</td>
                <td>{$error.total|escape:'htmlall':'UTF-8'}</td>
                <td colspan="2">{$error.error|escape:'htmlall':'UTF-8'}</td>
                <td>{$error.date_import|escape:'htmlall':'UTF-8'}</td>
                <td><a class="reSynchOrder" id="{$error.reference_ebay}">{l s='Réessayer' mod='ebay'}</a></td>
            </tr>
        {/foreach}
        {/if}
        {if isset($orders_tab)}
        {foreach from=$orders_tab item="order"}
            <tr>
                <td>{$order.date_ebay|escape:'htmlall':'UTF-8'}</td>
                <td>{$order.reference_ebay|escape:'htmlall':'UTF-8'}</td>
                <td>{$order.referance_marchand|escape:'htmlall':'UTF-8'}</td>
                <td>{$order.email|escape:'htmlall':'UTF-8'}</td>
                <td>{$order.total|escape:'htmlall':'UTF-8'}</td>
                <td >{$order.id_prestashop|escape:'htmlall':'UTF-8'}</td>
                <td >{$order.reference_ps|escape:'htmlall':'UTF-8'}</td>
                <td >{$order.date_import|escape:'htmlall':'UTF-8'}</td>
                <td ></td>
            </tr>
        {/foreach}
        {/if}
        </tbody>

    </table>

<script type="text/javascript">
    {literal}
   $('.reSynchOrder').live('click', function(e) {
       e.preventDefault();
       var tr = $(this).parent().parent();
       $.ajax({
           type: 'POST',
           url: module_dir + 'ebay/ajax/reSynchOrder.php',
           data: "token={/literal}{$ebay_token|escape:'urlencode'}{literal}&id_ebay_profile={/literal}{$id_ebay_profile|escape:'urlencode'}{literal}&id_order_ebay="+$(this).attr('id'),
           success: function (data) {
               var data = jQuery.parseJSON(data);
               console.log(data);
                var str = '<td>'+data.date_ebay+'</td><td>'+data.reference_ebay+'</td><td>'+data.referance_marchand+'</td><td>'+data.email+'</td><td>'+data.total+'</td>';
                if (typeof data.id_prestashop !== 'undefined' ){
                    str += '<td >'+data.id_prestashop+'</td><td >'+data.reference_ps+'</td><td >'+data.date_import+'</td><td ></td>';
                } else {
                    str += '<td colspan="2">'+data.error+'</td><td>'+data.date_import+'</td><td><a class="reSynchOrder" id="'+data.reference_ebay+'">Réessayer</a></td>';
                }
               console.log(str);
               tr.html(str);
           }
       });
   });
    {/literal}
</script>
