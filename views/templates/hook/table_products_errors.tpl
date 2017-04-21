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


        <p>Les produits ci-dessous ont été refusés par eBay. Attention si une annonce est déjà présente sur eBay, cette annonce n’est plus mise à jour.
            Une action manuelle est nécessaire pour corriger le refus de eBay, ou supprimez ce produit de votre synchronisation</p>

        <table id="AnnoncesErrorsListings" class="table" >
            <thead>
            <tr >

                <th style="width:110px;">
                    <span>{l s='Date' mod='ebay'}</span>
                </th>

                <th>
                    <span>{l s='ID' mod='ebay'}</span>
                </th>

                <th class="center">
                    <span>{l s='Nom' mod='ebay'}</span>
                </th>

                <th class="center">
                    <span >{l s='Declinaison' mod='ebay'}</span>
                </th>

                <th>
                    <span >{l s='Annonces eBay' mod='ebay'}</span>
                </th>

                <th class="center">
                    <span >{l s='Erreur' mod='ebay'}</span>
                </th>

                <th class="center">
                    <span >{l s='Description' mod='ebay'}</span>
                </th>

                <th class="center">
                    <span>{l s='Stock' mod='ebay'}</span>
                </th>

                <th class="center">
                    <span>{l s='Aide' mod='ebay'}</span>
                </th>

                <th class="center">{l s='Actions' mod='ebay'}</th>

            </tr>
            </thead>

            <tbody>
            {if isset($task_errors)}
                {foreach from=$task_errors item="task_error"}
                    <tr>
                        <td>{$task_error.date|escape:'htmlall':'UTF-8'}</td>
                        <td>{$task_error.id_product|escape:'htmlall':'UTF-8'}</td>
                        <td>{$task_error.name|escape:'htmlall':'UTF-8'}</td>
                        <td>{$task_error.declinason|escape:'htmlall':'UTF-8'}</td>
                        <td>{$task_error.id_item|escape:'htmlall':'UTF-8'}</td>
                        <td>{$task_error.error|escape:'htmlall':'UTF-8'}</td>
                        <td >{$task_error.desc_error|escape:'htmlall':'UTF-8'}</td>
                        <td ></td>
                        <td ><a class="kb-help"
                                data-errorcode="{$task_error.error_code}"
                                data-module="ebay"
                                data-lang="{$task_error.lang_iso}"
                                module_version="1.11.0"
                                prestashop_version="{$task_error.ps_version}"></a></td>
                        <td ><a class="btn btn-xs btn-block btn-warning" id="{$task_error.id_product}" href="{$task_error.product_url}" target="_blank"><i class="icon-gavel"></i>{l s='Corrige' mod='ebay'}</a>
                            <a class="btn btn-xs btn-block btn-danger exclure_product" id="{$task_error.id_product}"><i class="icon-ban"></i>{l s='Exclure' mod='ebay'}</a></td>
                    </tr>
                {/foreach}
            {/if}
            </tbody>

        </table>
{literal}
<script>
    $('.exclure_product').click(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: module_dir + 'ebay/ajax/exclureProductAjax.php',
            data: "token={/literal}{$ebay_token|escape:'urlencode'}{literal}&id_ebay_profile={/literal}{$id_ebay_profile|escape:'urlencode'}{literal}&id_product="+$(this).attr('id'),
            success: function (data) {
                $(this).parent().parent().remove();
            }
        });
    });
    {/literal}
</script>