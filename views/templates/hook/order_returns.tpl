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
* @author 202-ecommerce <tech@202-ecommerce.com>
* @copyright Copyright (c) 2017-2020 202-ecommerce
* @license Commercial license
*  International Registered Trademark & Property of PrestaShop SA
*}


<div class="table-block">
    <h4 class="table-block__title table-block__holder">{l s='Returns imported by ' mod='ebay'} {$type_sync_returns}
        <a href="{$url|escape:'htmlall':'UTF-8'}&EBAY_SYNC_ORDERS_RETURNS=1"
           class="button-refresh btn btn-default"
           title="{l s='Sync refunds and returns from eBay' mod='ebay'}"
           data-toggle="tooltip">
           <span class="icon-refresh"></span> {l s='Sync' mod='ebay'}
        </a>
    </h4>

    {if empty($returns)}
        <div class="table-block__message table-block__holder">
            <div class="table-block__message-holder">
                <p>{l s='No imported returns' mod='ebay'}</p>
            </div>
        </div>
    {else}
        <p class="table-block__holder" style="margin:0;padding: 5px 0;">{l s='Last import:' mod='ebay'} {$date_last_import|escape:'htmlall':'UTF-8'}</p>

        <div id="OrderReturnsBlock">
            {include file=$tpl_returns_ajax}
        </div>
    {/if}
</div>




{* Bootstrap tooltip *}
<script>
    var ebayOrdersController = "{$ebayOrdersController|addslashes}";
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
