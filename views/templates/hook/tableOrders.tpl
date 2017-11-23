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
    <h4 class="table-block__title table-block__holder">{l s='Add desctiption in ' mod='ebay'} {$type_sync_order}
        <a href="{$url|escape:'htmlall':'UTF-8'}&EBAY_SYNC_ORDERS_RETURNS=1"
           class="button-refresh btn btn-default"
           title="{l s='Sync refunds and returns from eBay' mod='ebay'}"
           data-toggle="tooltip">
            <span class="icon-refresh"></span> {l s='Sync' mod='ebay'}
        </a>
    </h4>
    {if empty($orders_tab) && empty($errors)}
        <div class="table-block__message table-block__holder">
            <div class="table-block__message-holder">
                <p>{l s='No imported orders' mod='ebay'}</p>
            </div>
        </div>
    {else}
        <p class="table-block__holder" style="margin:0;padding: 5px 0;">{l s='Last import:' mod='ebay'} {$date_last_import|escape:'htmlall':'UTF-8'}</p>

        <div class="table-wrapper">
            <table id="OrderListings" class="table ">
                <thead>
                    <tr>
                        <th style="width:110px;"><span>{l s='Data of creation' mod='ebay'}</span></th>
                        <th><span>{l s='eBay reference' mod='ebay'}</span></th>
                        <th class="text-center"><span>{l s='Payment' mod='ebay'}</span></th>
                        <th class="text-center"><span>{l s='Email' mod='ebay'}</span></th>
                        <th><span>{l s='Total' mod='ebay'}</span></th>
                        <th class="text-center"><span>{l s='ID' mod='ebay'}</span></th>
                        <th class="text-center"><span>{l s='PrestaShop Reference' mod='ebay'}</span></th>
                        <th class="text-center"><span>{l s='Date Import' mod='ebay'}</span></th>
                        <th class="text-center">{l s='Actions' mod='ebay'}</th>
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
                            <td>{$currency->sign}{$error.total|string_format:"%.2f"|escape:'htmlall':'UTF-8'}</td>
                            <td colspan="2">{$error.error|escape:'htmlall':'UTF-8'}</td>
                            <td>{$error.date_import|escape:'htmlall':'UTF-8'}</td>
                            <td>
                               <div class="action">
                                   <a class="reSynchOrder btn btn-default btn-sm" id="{$error.reference_ebay}"
                                      title="{l s='Sync refunds and returns from eBay' mod='ebay'}"
                                      data-toggle="tooltip">
                                       <i class="icon-refresh"></i>
                                   </a>
                                   <button class="deleteOrderError btn btn-default btn-sm btn-hover-danger"
                                           data-error="{$error.reference_ebay}"
                                           title="{l s='Delete' mod='ebay'}"
                                           data-toggle="tooltip">
                                       <i class="icon-close"></i>
                                   </button>
                               </div>
                            </td>
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
                            <td>{$currency->sign}{$order.total|string_format:"%.2f"|escape:'htmlall':'UTF-8'}</td>
                            <td>{$order.id_prestashop|escape:'htmlall':'UTF-8'}</td>
                            <td>{$order.reference_ps|escape:'htmlall':'UTF-8'}</td>
                            <td>{$order.date_import|escape:'htmlall':'UTF-8'}</td>
                            <td></td>
                        </tr>
                    {/foreach}
                {/if}
                </tbody>
            </table>
        </div>
    {/if}
</div>

<script type="text/javascript">
    {literal}
    $('.reSynchOrder').live('click', function (e) {
        e.preventDefault();
        var tr = $(this).closest('tr');
        $.ajax({
            type: 'POST',
            url: module_dir + 'ebay/ajax/reSynchOrder.php',
            data: "token={/literal}{$ebay_token}{literal}&id_ebay_profile={/literal}{$id_ebay_profile}{literal}&id_order_ebay=" + $(this).attr('id'),
            success: function (data) {
                var data = jQuery.parseJSON(data);

                var str = '<td>' + data.date_ebay + '</td><td>' + data.reference_ebay + '</td><td>' + data.referance_marchand + '</td><td>' + data.email + '</td><td>' + data.total + '</td>';
                if (typeof data.id_prestashop !== 'undefined') {
                    str += '<td >' + data.id_prestashop + '</td><td >' + data.reference_ps + '</td><td >' + data.date_import + '</td><td ></td>';
                } else {
                    str += '<td colspan="2">' + data.error + '</td><td>' + data.date_import + '</td><td><a class="reSynchOrder btn btn-default btn-xs" id="' + data.reference_ebay + '"><i class="icon-refresh"></i><span>Réessayer</span></a></td>';
                }

                tr.html(str);
            }
        });
    });

    $('.deleteOrderError').click(deleteOrderError);

    function deleteOrderError() {
        var reference_ebay;
        var tr;

        reference_ebay = $(this).attr('data-error');
        tr = $(this).closest('tr');
        $.ajax({
            type: 'POST',
            url: module_dir + 'ebay/ajax/deleteOrderError.php',
            data: {
                reference_ebay: reference_ebay,
                token: '{/literal}{$ebay_token}{literal}',
            },
            success: function (data) {
                console.log(data);
                $(tr).remove();
            },
        });

    }
    {/literal}
</script>
