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

<div class="table-wrapper" id="OrderListingsBlock">
    <table id="OrderListings" class="table">
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
                {if !isset($order.error)}
                    <tr>
                        <td>{$order.date_ebay|escape:'htmlall':'UTF-8'}</td>
                        <td>{$order.reference_ebay|escape:'htmlall':'UTF-8'}</td>
                        <td>{$order.referance_marchand|escape:'htmlall':'UTF-8'}</td>
                        <td>{$order.email|escape:'htmlall':'UTF-8'}</td>
                        <td>{$currency->sign}{$order.total|string_format:"%.2f"|escape:'htmlall':'UTF-8'}</td>
                        <td>{$order.id_prestashop|escape:'htmlall':'UTF-8'}</td>
                        <td><a href="{$order.order_link}">{$order.reference_ps|escape:'htmlall':'UTF-8'}</a></td>
                        <td>{$order.date_import|escape:'htmlall':'UTF-8'}</td>
                    </tr>
                {/if}
            {/foreach}
        {/if}

        </tbody>
    </table>
    {if isset($pages_all) and $pages_all >1}
        <div class="navPaginationListOrdersHistory" style="display:flex; justify-content:center">
            {include file=$tpl_include}
        </div>
    {/if}
</div>
<script>
    $('.navPaginationListOrdersHistory .pagination span').click(function () {
        var page = $(this).attr('value');
        var orderListings  = $('#OrderListings');
        if (page) {
            $.ajax({
                type: "POST",
                url: ebayOrdersController,
                data: {
                    id_employee: id_employee,
                    id_ebay_profile: id_ebay_profile,
                    page: page,
                    ajax: true,
                    action: 'LoadOrderHistory',
                },
                beforeSend: function () {
                    orderListings.empty();
                    var html = '<div class="ajaxLoadingFormSyncTab" style="position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px; margin-left: -30px;"></div>';
                    orderListings.append(html);
                },
                success: function (data) {
                    $('#OrderListingsBlock').fadeOut(400, function () {
                        $(this).html(data).fadeIn();
                    })
                },
            });
        }
    });
</script>