{*
* 2007-2022 PrestaShop
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
<div class="table-wrapper" id="OrderReturnsBlock">
    <table id="OrderReturns" class="table" cellpadding="0" cellspacing="0" style="width: 100%;">
        <thead>
        <tr class="nodrag nodrop">
            <th style="width:110px;"><span>{l s='PrestaShop Order' mod='ebay'}</span></th>
            <th style="width:110px;"><span>{l s='EBay Order' mod='ebay'}</span></th>
            <th class="text-center"><span>{l s='Description' mod='ebay'}</span></th>
            <th class="text-center"><span>{l s='Status' mod='ebay'}</span></th>
            <th class="text-center"><span>{l s='Type' mod='ebay'}</span></th>
            <th class="text-center"><span>{l s='Date' mod='ebay'}</span></th>
            <th class="text-center"><span>{l s='Id Product' mod='ebay'}</span></th>
        </tr>
        </thead>

        <tbody>
        {foreach from=$returns item="return"}
            <tr>
                <td>{$return.id_order|escape:'htmlall':'UTF-8'}</td>
                <td>{$return.id_ebay_order|escape:'htmlall':'UTF-8'}</td>
                <td>{$return.description|escape:'htmlall':'UTF-8'}</td>
                <td>{$return.status|escape:'htmlall':'UTF-8'}</td>
                <td>{$return.type|escape:'htmlall':'UTF-8'}</td>
                <td>{$return.date|escape:'htmlall':'UTF-8'}</td>
                <td>{$return.id_item|escape:'htmlall':'UTF-8'}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {if isset($pages_all) and $pages_all >1}
        <div class="navPaginationListOrdersReturnsHistory" style="display:flex; justify-content:center">
            {include file=$tpl_include}
        </div>
    {/if}
</div>
<script>
    $('.navPaginationListOrdersReturnsHistory .pagination span').click(function () {
        var page = $(this).attr('value');
        var orderListings  = $('#OrderReturns');
        if (page) {
            $.ajax({
                type: "POST",
                url: ebayOrdersController,
                data: {
                    id_employee: id_employee,
                    id_ebay_profile: id_ebay_profile,
                    page: page,
                    ajax: true,
                    action: 'LoadReturnsHistory',
                },
                beforeSend: function () {
                    orderListings.empty();
                    var html = '<div class="ajaxLoadingFormSyncTab" style="position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px; margin-left: -30px;"></div>';
                    orderListings.append(html);
                },
                success: function (data) {
                    $('#OrderReturnsBlock').fadeOut(400, function () {
                        $(this).html(data).fadeIn();
                    })
                },
            });
        }
    });
</script>