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
        <p class="table-block__holder" style="margin:0;padding: 5px 0;">{l s='Last import :' mod='ebay'} {$date_last_import|escape:'htmlall':'UTF-8'}</p>

        <div class="table-wrapper">
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
        </div>
    {/if}
</div>




{* Bootstrap tooltip *}
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
