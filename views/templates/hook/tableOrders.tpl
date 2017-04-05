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



    <!-- table -->

    <table id="OrphanListings" class="table " >
        <thead>
        <tr >

            <th style="width:110px;">
                <span>{l s='Data eBay' mod='ebay'}</span>
            </th>

            <th>
                <span>{l s='Referance eBay' mod='ebay'}</span>
            </th>

            <th class="center">
                <span>{l s='Referance b' mod='ebay'}</span>
            </th>

            <th class="center">
                <span >{l s='email' mod='ebay'}</span>
            </th>

            <th>
                <span >{l s='Total' mod='ebay'}</span>
            </th>


            <th class="center">
                <span >{l s='ID PrestaShop' mod='ebay'}</span>
            </th>

            <th class="center">
                <span>{l s='PrestaShop referance' mod='ebay'}</span>
            </th>

            <th class="center">
                <span>{l s='Date Import' mod='ebay'}</span>
            </th>

            <th class="center">{l s='Actions' mod='ebay'}</th>

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
                <td>{$error.total|escape:'htmlall':'UTF-8'}</td>
                <td colspan="2">{$error.error|escape:'htmlall':'UTF-8'}</td>
                <td>{$error.date_import|escape:'htmlall':'UTF-8'}</td>
                <td><a id="{$error.reference_ebay}">{l s='Réessayer' mod='ebay'}</a></td>
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
                <td>{$order.total|escape:'htmlall':'UTF-8'}</td>
                <td >{$order.id_prestashop|escape:'htmlall':'UTF-8'}</td>
                <td >{$order.reference_ps|escape:'htmlall':'UTF-8'}</td>
                <td >{$order.date_import|escape:'htmlall':'UTF-8'}</td>
                <td ></td>
            </tr>
        {/foreach}
        {/if}
        </tbody>

    </table>

<script type="text/javascript">

    var orphan_listings_ebay_l = {ldelim}
        'Remove this ad?': "{l s='End this listing?' mod='ebay'}"
        {rdelim};
</script>
<script type="text/javascript" src="{$_module_dir_|escape:'htmlall':'UTF-8'}ebay/views/js/orphanListings.js?date={$date|escape:'htmlall':'UTF-8'}"></script>
