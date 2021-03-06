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

<div class="col-md-9">


<br>
<h4>{l s='Products' mod='ebay'}</h4>
    {l s='Number of PrestaShop categories:' mod='ebay'} {$nb_categories}<br>
    {l s='Number of excluded products:' mod='ebay'} {$nb_products_exclu}<br>
   
    {l s='Number of eBay listings:' mod='ebay'} {$nb_products}<br>
    {l s='Numbers of errors:' mod='ebay'} {$count_product_errors}<br>
    {l s='Synchronization:' mod='ebay'} {$type_sync_product}<br>
    {l s='Updates to send:' mod='ebay'}  {$nb_tasks}<br>
<br>
<h4>{l s='International passive sales' mod='ebay'}: {if $country_shipping}{l s='YES' mod='ebay'}{else}{l s='NO' mod='ebay'}{/if}</h4>
    {if $country_shipping}
        {l s='Countries:' mod='ebay'}<br>
        {foreach from=$country_shipping item=country}
            {$country['description']} <br>
        {/foreach}
    {/if}
<br>
<h4>{l s='Orders' mod='ebay'}</h4>
    {l s='CA eBay 30 last days:' mod='ebay'} {$ca_total} <br>
    {l s='Errors orders:' mod='ebay'} {$count_order_errors}<br>
    {l s='Last import:' mod='ebay'} {$date_last_import} par {$type_sync_order}<br>
</div>
