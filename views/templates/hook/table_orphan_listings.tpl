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


<div id="ebayOrphanReListing">
    <p class="center">
        <button class="button loadListOrphan btn btn-default">{l s='Refresh Listing' mod='ebay'}</button>
        <button class="delete_all_orphans btn btn-default">{l s='Delete all' mod='ebay'}</button>
        <button class="delete_several_orphans btn btn-default">{l s='Delete selected products' mod='ebay'}</button>
    </p>
</div>

</br>

<table id="OrphanListings" class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%;">
    <thead>
    <tr class="nodrag nodrop">
        
        <th></th>

        <th style="width:110px;">
            <span>{l s='eBay Listing' mod='ebay'}</span>
        </th>

        <th>
            <span>{l s='PrestaShop Product' mod='ebay'}</span>
        </th>

        <th class="center">
            <span data-inlinehelp="{l s='Product has been disabled in the PrestaShop product page' mod='ebay'}">{l s='Deactivated product' mod='ebay'}</span>
        </th>

        <th class="center">
            <span data-inlinehelp="{l s='Product has been disabled in the PrestaShop product page' mod='ebay'}">{l s='Excluded product' mod='ebay'}</span>
        </th>

        <th class="center">
            <span data-inlinehelp="{l s='Does PrestaShop product have combinations' mod='ebay'}">{l s='Product Combinations' mod='ebay'}</span>
        </th>

        <th>
            <span data-inlinehelp="{l s='PrestaShop product default category' mod='ebay'}">{l s='PrestaShop Category' mod='ebay'}</span>
        </th>

        <th class="center">
            <span data-inlinehelp="{l s='eBay category associated with PrestaShop product\'s default category' mod='ebay'}">{l s='eBay Category' mod='ebay'}</span>
        </th>

        <th class="center">
            <span data-inlinehelp="{l s='Does eBay category support multivariation listings ?' mod='ebay'}">{l s='Category Multi-sku' mod='ebay'}</span>
        </th>

        <th class="center">
            <span data-inlinehelp="{l s='If this column is set to \'no\', product default category has not been synchronised in \'Synchronisation > 1. List products\' tab' mod='ebay'}">{l s='Synchronisation Enabled' mod='ebay'}</span>
        </th>

        <th class="center">
            <span>{l s='Action' mod='ebay'}</span>
        </th>

        <th class="center">{l s='Help' mod='ebay'}</th>

    </tr>
    </thead>

    <tbody>
{if $ads === false || sizeof($ads) === 0}

	<tr>
		<td colspan="10" class="center">{l s='No orphan listing' mod='ebay'}</td>
	</tr>

{else}

	{foreach from=$ads key=k  item=a}
        {if $a.id_task == 14}
            {continue}
        {/if}
		<tr{if $k % 2 !== 0} class="alt_row"{/if}>
            
            <td>
                <input type="checkbox" class="checkboxfordelete">
            </td>

			<td>
                {if $a.id_product_ref}
                    <a style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis" href="{$a.link|escape:'htmlall':'UTF-8'}" target="_blank">{$a.id_product_ref|escape:'htmlall':'UTF-8'}</a>
                {/if} 
			</td>

            <td>{if $a.exists}{$a.psProductName|escape:'htmlall':'UTF-8'}{else}{l s='Product deleted. Id: ' mod='ebay'}{$a.id_product|escape:'htmlall':'UTF-8'}{/if}</td>
            
            {if $a.exists}
                <td class="center">{if $a.active && !$a.blacklisted}{l s='No' mod='ebay'}{else}<span style="color: red;">{l s='-' mod='ebay'}</span>{/if}</td>
                <td class="center">{if $a.active && !$a.blacklisted}{l s='-' mod='ebay'}{else}<span style="color: red;">{l s='Yes' mod='ebay'}</span>{/if}</td>
            {else}
                <td class="center">-</td>
            {/if}                
            
            {if $a.exists}
                <td class="center">{if $a.isMultiSku}{l s='Yes' mod='ebay'}{else}{l s='No' mod='ebay'}{/if}</td>
            {else}
                <td class="center">-</td>
            {/if}                
            
            {if $a.exists}
                <td>{$a.category_full_name|escape:'htmlall':'UTF-8'}</td>
            {else}
                <td class="center">-</td>
            {/if}                

            {if $a.exists && $a.id_category_ref}
                <td>{$a.ebay_category_full_name|escape:'htmlall':'UTF-8'}</td>
            {else}
                <td class="center">-</td>
            {/if}
            
            {if $a.exists && $a.id_category_ref}
                <td class="center">{if $a.EbayCategoryIsMultiSku}{l s='Yes' mod='ebay'}{else}{l s='No' mod='ebay'}{/if}</td>
            {else}
                <td class="center">-</td>
            {/if}

            {if $a.exists && $a.id_category_ref}
                <td class="center">{if $a.sync}{l s='Yes' mod='ebay'}{else}<span style="color: red;">{l s='No' mod='ebay'}</span>{/if}</td>
            {else}
                <td class="center">-</td>
            {/if}
            
            <td class="center">
                <a href="#" class="delete-orphan" ref="{$a.id_product_ref|escape:'htmlall':'UTF-8'}"><img src="../img/admin/delete.gif" /></a>
                {if $a.exists && $a.id_category_ref && $a.sync && !$a.active}
                    <a href="#" class="out_of_stock_orphan btn btn-xs btn-block btn-danger" ref="{$a.id_product_ref|escape:'htmlall':'UTF-8'}"><i class="icon-ban"></i>{l s='OUT OF STOCK' mod='ebay'}</a></a>
                {/if}
            </td>
            
            <td>
                {if !$a.exists}
                    {l s='PrestaShop Product does not exists' mod='ebay'}
                {/if}
            </td>
            
	{/foreach}

{/if}
    </tbody>

</table>
{if isset($pages_all) and $pages_all >1}
    <div class="navPaginationListOrphanProductTab" style="display:flex; justify-content:center">
        {include file=$tpl_include}
    </div>
{/if}
</div>


<div id="popin-delete-all-products" class="popin popin-sm" style="display: none;position: fixed;z-index: 1;left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgb(0,0,0);background-color: rgba(0,0,0,0.4);">
    <div class="panel" style=" background-color: #fefefe;padding: 20px;border: 1px solid #888;width: 80%;margin: 30% auto;">
        <div class="panel-heading">
            <i class="icon-trash"></i> {l s='Orphan Listing' mod='ebay'}
        </div>
        <p>{l s='Remove all products?' mod='ebay'}</p>

        <div class="panel-footer" style="display: flex; justify-content: space-between; align-items: center">
            <button class="cancel-delete btn btn-default"><i class="process-icon-cancel"></i>Annuler</button>
            <button class="ok-delete btn btn-success pull-right" style="padding:15px">OK</button>
        </div>
    </div>
</div>

<div id="popin-delete-product" class="popin popin-sm" style="display: none;position: fixed;z-index: 1;left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgb(0,0,0);background-color: rgba(0,0,0,0.4);">
    <div class="panel" style=" background-color: #fefefe;padding: 20px;border: 1px solid #888;width: 80%;margin: 30% auto;">
        <div class="panel-heading">
            <i class="icon-trash"></i> {l s='Orphan Listing' mod='ebay'}
        </div>
        <p>{l s='Remove this product?' mod='ebay'}</p>

        <div class="panel-footer" style="display: flex; justify-content: space-between; align-items: center">
            <button class="cancel-delete btn btn-default"><i class="process-icon-cancel"></i>Annuler</button>
            <button class="ok-delete btn btn-success pull-right" style="padding:15px">OK</button>
        </div>
    </div>
</div>

<div id="popin-delete-several-products" class="popin popin-sm" style="display: none;position: fixed;z-index: 1;left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgb(0,0,0);background-color: rgba(0,0,0,0.4);">
    <div class="panel" style=" background-color: #fefefe;padding: 20px;border: 1px solid #888;width: 80%;margin: 30% auto;">
        <div class="panel-heading">
            <i class="icon-trash"></i> {l s='Orphan Listing' mod='ebay'}
        </div>
        <p>{l s='Remove selected products?' mod='ebay'}</p>

        <div class="panel-footer" style="display: flex; justify-content: space-between; align-items: center">
            <button class="cancel-delete btn btn-default"><i class="process-icon-cancel"></i>Annuler</button>
            <button class="ok-delete btn btn-success pull-right" style="padding:15px">OK</button>
        </div>
    </div>
</div>

<script type="text/javascript">



</script>