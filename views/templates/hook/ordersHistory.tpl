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

<h2>{l s='Here is a record of your eBays orders' mod='ebay'} :</h2>
<p>
    <b>{$date_last_import|escape:'htmlall':'UTF-8'}</b>
</p>
<br/>
<br/>
<h2>{l s='Orders History' mod='ebay'} :</h2>
<div class="ebay_mind big">{l s='If you have orders with NOSEND-EBAY in the email client, you can contact us to open a support ticket.' mod='ebay'}
    <a class="kb-help" data-errorcode="HELP-VISUALIZATION-NO-SEND-EBAY" data-module="ebay" data-lang="{$help.lang|escape:'htmlall':'UTF-8'}"
       module_version="{$help.module_version|escape:'htmlall':'UTF-8'}" prestashop_version="{$help.ps_version|escape:'htmlall':'UTF-8'}"></a></div>
{if count($orders)}
    {foreach from=$orders item="order"}
        <style>
            {literal}
            .orderImportTd1 {
                border-right: 1px solid #000
            }

            .orderImportTd2 {
                border-right: 1px solid #000;
                border-top: 1px solid #000
            }

            .orderImportTd3 {
                border-top: 1px solid #000
            }

            {/literal}
        </style>
        <p>
            <b>{l s='Order Ref eBay' mod='ebay'} :</b>
            {if $order.id_order_ref}{$order.id_order_ref|escape:'htmlall':'UTF-8'}{/if}
            <br/>
            <b>{l s='Id Order Seller' mod='ebay'} :</b>
            {if $order.id_order_seller}{$order.id_order_seller|escape:'htmlall':'UTF-8'}{/if}
            <br/>
            <b>{l s='Amount' mod='ebay'} :</b>
            {if $order.amount}{$order.amount|escape:'htmlall':'UTF-8'}{/if}
            <br/>
            <b>{l s='Status' mod='ebay'} :</b>
            {if $order.status}{$order.status|escape:'htmlall':'UTF-8'}{/if}
            <br/>
            <b>{l s='Date' mod='ebay'} :</b>
            {if $order.date}{$order.date|escape:'htmlall':'UTF-8'}{/if}
            <br/>
            <b>{l s='E-mail' mod='ebay'} :</b>
            {if $order.email}{$order.email|escape:'htmlall':'UTF-8'}{/if}
            <br/>
            <b>{l s='Products' mod='ebay'} :</b>
            <br/>
            {if $order.products && ($order.products|count > 0)}
                <table border="0" cellpadding="4" cellspacing="0">
                    <tr>
                        <td class="orderImportTd1">
                            <b>{l s='Id Product' mod='ebay'}</b>
                        </td>
                        <td class="orderImportTd1">
                            <b>{l s='Id Product Attribute' mod='ebay'}</b>
                        </td>
                        <td class="orderImportTd1">
                            <b>{l s='Quantity' mod='ebay'}</b>
                        </td>
                        <td>
                            <b>{l s='Price' mod='ebay'}</b>
                        </td>
                    </tr>
                    {foreach from=$order.products item="product"}
                        <tr>
                            <td class="orderImportTd2">{$product.id_product|escape:'htmlall':'UTF-8'}</td>
                            <td class="orderImportTd2">{$product.id_product|escape:'htmlall':'UTF-8'}</td>
                            <td class="orderImportTd2">{$product.quantity|escape:'htmlall':'UTF-8'}</td>
                            <td class="orderImportTd3">{$product.price|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                    {/foreach}
                </table>
            {/if}
            {if $order.error_messages|count}
                <b>{l s='Status Import' mod='ebay'} :</b>
                KO
                <br/>
                <b>{l s='Failure details' mod='ebay'} :</b>
                <br/>
                {foreach from=$order.error_messages item="error"}
                    {$error|escape:'htmlall':'UTF-8'}
                    <br/>
                {/foreach}
            {else}
                <b>{l s='Status Import' mod='ebay'} :</b>
                OK
            {/if}
        </p>
        <br/>
    {/foreach}
{/if}		
