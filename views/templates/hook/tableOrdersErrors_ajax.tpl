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
* @copyright Copyright (c) 2007-2022 202-ecommerce
* @license Commercial license
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="table-wrapper" id="OrderErrorsListingsBlock">
    <table id="OrderErrorsListings" class="table">
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
            {if isset($errors)}
                <th class="text-center">{l s='Actions' mod='ebay'}</th>
            {/if}
        </tr>
        </thead
        <tbody>
        {if isset($all_orders)}
            {foreach from=$all_orders item="order"}
                {if isset($order.error) && $order.error != null}
                    <tr>
                        <td>{$order.date_ebay|escape:'htmlall':'UTF-8'}</td>
                        <td>{$order.reference_ebay|escape:'htmlall':'UTF-8'}</td>
                        <td>{$order.referance_marchand|escape:'htmlall':'UTF-8'}</td>
                        <td>{$order.email|escape:'htmlall':'UTF-8'}</td>
                        <td>{$currency->sign}{$order.total|string_format:"%.2f"|escape:'htmlall':'UTF-8'}</td>
                        <td colspan="2">{$order.error|escape:'htmlall':'UTF-8'}</td>
                        <td>{$order.date_import|escape:'htmlall':'UTF-8'}</td>
                        <td>
                            <div class="action">
                                <a class="reSynchOrder btn btn-default btn-sm" id="{$order.reference_ebay|addslashes}"
                                   title="{l s='ReSync Order from eBay' mod='ebay'}"
                                   data-toggle="tooltip">
                                    <i class="icon-refresh"></i>
                                </a>
                                <button class="deleteOrderError btn btn-default btn-sm btn-hover-danger"
                                        data-error="{$order.reference_ebay|addslashes}"
                                        title="{l s='Delete' mod='ebay'}"
                                        data-toggle="tooltip">
                                    <i class="icon-close"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                {/if}
            {/foreach}
        {/if}

        </tbody>
    </table>
    {if isset($pages_all) and $pages_all >1}
        <div class="navPaginationListOrdersErrorsHistory" style="display:flex; justify-content:center">
            {include file=$tpl_include}
        </div>
    {/if}
</div>
<script>
    {literal}

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
    $('.navPaginationListOrdersErrorsHistory .pagination span').click(function () {
        var pageErrors = $(this).attr('value');
        var OrderErrorsListings  = $('#OrderErrorsListings');
        if (pageErrors) {
            $.ajax({
                type: "POST",
                url: ebayOrdersController,
                data: {
                    id_employee: id_employee,
                    id_ebay_profile: id_ebay_profile,
                    page: pageErrors,
                    ajax: true,
                    action: 'LoadOrderErrorsHistory',
                },
                beforeSend: function () {
                    OrderErrorsListings.empty();
                    var html = '<div class="ajaxLoadingFormSyncTab" style="position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px; margin-left: -30px;"></div>';
                    OrderErrorsListings.append(html);
                },
                success: function (data) {
                    $('#OrderErrorsListingsBlock').fadeOut(400, function () {
                        $(this).html(data).fadeIn();
                    })
                },
            });
        }
    });
</script>