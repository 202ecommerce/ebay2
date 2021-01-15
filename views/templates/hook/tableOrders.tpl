{*
* 2007-2021 PrestaShop
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
* @copyright Copyright (c) 2007-2021 202-ecommerce
* @license Commercial license
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="table-block">
    <h4 class="table-block__title table-block__holder">{l s='Add a description in ' mod='ebay'} {$type_sync_order}
        <a href="{$url|escape:'htmlall':'UTF-8'}&EBAY_SYNC_ORDERS=1"
           class="button-refresh btn btn-default"
           title="{l s='Sync orders from eBay' mod='ebay'}"
           data-toggle="tooltip">
            <span class="icon-refresh"></span> {l s='Sync' mod='ebay'}
        </a>
    </h4>
    {if empty($all_orders)}
        <div class="table-block__message table-block__holder">
            <div class="table-block__message-holder">
                <p>{l s='No imported orders' mod='ebay'}</p>
            </div>
        </div>
    {else}
        <p class="table-block__holder" style="margin:0;padding: 5px 0;">{l s='Last import:' mod='ebay'} {$date_last_import|escape:'htmlall':'UTF-8'}</p>

        <div id="OrderListingsBlock">
            {include file=$tpl_orders_ajax}
        </div>


    {/if}
</div>

<script type="text/javascript">
    var ebayOrdersController = "{$ebayOrdersController|addslashes}";
    {literal}
    $('.reSynchOrder').live('click', function (e) {
        e.preventDefault();
        var tr = $(this).closest('tr');
        $.ajax({
            type: 'POST',
            url: ebayOrdersController,
            data: "ajax=true&action=ResyncOrder&id_ebay_profile={/literal}{$id_ebay_profile}{literal}&id_order_ebay=" + $(this).attr('id'),
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
            url: ebayOrdersController,
            data: {
                reference_ebay: reference_ebay,
                ajax: true,
                action: 'DeleteOrderError',
            },
            success: function (data) {
                console.log(data);
                $(tr).remove();
            },
        });

    }
    {/literal}
</script>
