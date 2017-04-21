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


<div class="pull-left">
    <div class="dropdown js-user-dropdown">

        <button class="form-control dropdown-toggle" type="button" data-toggle="dropdown" style="
    overflow: hidden;
    background: transparent;
    border: none;
">
            <img src="{$path|escape:'htmlall':'UTF-8'}logo.png" alt="" style="
    margin-top: -20px;
"/>
            <span class="badge badge-danger" style="
    /* display: inline-block; */
    vertical-align: top;
    margin-left: -9px;
    margin-top: -4px;
">{if $nb_errors > 0}{$nb_errors|escape:'htmlall':'UTF-8'}{/if}</span>
        </button>

        <ul class="dropdown-menu" style="
    min-width: 397px;
">
            <li class="clearfix head">
                <span class="col-xs-4">utilisateur</span>
                <span class="col-xs-3">pays</span>
                <span class="col-xs-3 text-right">annonces</span>
                <span class="col-xs-2 text-right">erreurs</span>
            </li>
            {foreach from=$profiles item=profile}

                <li>
                    <a class="clearfix" href="#" data-value="{$profile.id_ebay_profile|escape:'htmlall':'UTF-8'}">
                        <span id = 'name' class="col-xs-4">{$profile.ebay_user_identifier|escape:'htmlall':'UTF-8'}</span>
                        <span class="col-xs-3">{$profile.site_name|escape:'htmlall':'UTF-8'}</span>
                        <span class="col-xs-3 text-right"><span class="badge badge-success">{$profile.nb_products}</span></span>
                        <span class="col-xs-2 text-right"><span class="badge badge-danger">{$profile.count_product_errors}</span></span>
                    </a>
                </li>
            {/foreach}

        </ul>
    </div>
</div>

<script>

    // Select item from user dropdown
    $(document).on('click', '.js-user-dropdown .dropdown-menu li a', function() {
        var url_ebay = "{$url_ebay}";
        $(location).attr('href',url_ebay);
    });

    /* $('.ebay_profils').change(function() {
     $('.change_profile').find("input[name=ebay_profile]").val($( ".ebay_profils option:selected").attr('id'));
     $('.change_profile').submit();
     });*/
</script>