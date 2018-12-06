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
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $visible_logo}
    <div class="pull-left totebay">
        <div class="dropdown js-user-dropdown">
            <button class="dropdown-toggle" type="button" data-toggle="dropdown" style="height: 35px; overflow: hidden; background-color: transparent !important; border: none; outline: none;">
                <img src="{$path|escape:'htmlall':'UTF-8'}views/img/logo2.png" alt="" style="margin-top: -13px;"/>
                <span class="badge badge-danger" style="vertical-align: top; margin-left: -9px; margin-top: -4px;">{if $nb_errors > 0}{$nb_errors|escape:'htmlall':'UTF-8'}{/if}</span>
            </button>

        <ul class="dropdown-menu dropdown-menu-marker" style="min-width: 560px;">
            <li class="clearfix head">
                <span class="col-xs-5" style="position: relative; float: left; padding-left: 20px; padding-right: 5px; min-height: 1px; width: 170px; font-weight: 600;">{l s='User' mod='ebay'}</span>
                <span class="col-xs-2" style="position: relative; float: left; padding-left: 5px; padding-right: 5px; min-height: 1px; width: 100px; font-weight: 600;">{l s='Site ebay' mod='ebay'}</span>
                <span class="col-xs-2 text-right" style="position: relative; float: left; padding-left: 5px; padding-right: 5px; min-height: 1px; width: 70px;"><small>{l s='Listings' mod='ebay'}</small></span>
                <span class="col-xs-1 text-right" style="position: relative; float: left; padding-left: 5px; padding-right: 5px; min-height: 1px; width: 70px;"><small>{l s='Listing errors' mod='ebay'}</small></span>
                <span class="col-xs-1 text-right" style="position: relative; float: left; padding-left: 5px; padding-right: 5px; min-height: 1px; width: 70px;"><small>{l s='Order errors' mod='ebay'}</small></span>
                <span class="col-xs-1 text-right" style="position: relative; float: left; padding-left: 5px; padding-right: 5px; min-height: 1px; width: 70px;"><small>{l s='Tasks' mod='ebay'}</small></span>
            </li>
            {foreach from=$profiles item=profile}

                <li>
                    <a class="clearfix" href="#" data-value="{$profile.id_ebay_profile|escape:'htmlall':'UTF-8'}" style="padding: 5px 0;">
                        <span id='name' class="col-xs-5" style="position: relative; float: left; padding-left: 20px; padding-right: 5px; min-height: 1px; width: 170px;">{$profile.ebay_user_identifier|escape:'htmlall':'UTF-8'}</span>
                        <span class="col-xs-2" style="position: relative; float: left; padding-left: 5px; padding-right: 5px; min-height: 1px; width: 100px;">{$profile.site_name|escape:'htmlall':'UTF-8'}</span>
                        {if $profile.token == 0 }
                            <span class="col-xs-5 text-right" style="position: relative; float: left; padding-left: 5px; padding-right: 5px; min-height: 1px; width: 170px;">{l s='Not associated with Ebay' mod='ebay'}</span>
                        {elseif $profile.category == 0}
                            <span class="col-xs-5 text-right" style="position: relative; float: left; padding-left: 5px; padding-right: 5px; min-height: 1px; width: 170px;">{l s='No category configured' mod='ebay'}</span>
                        {else}
                            <span class="col-xs-2 text-right" style="position: relative; float: left; padding-left: 5px; padding-right: 5px; min-height: 1px; width: 70px;"><span class="badge badge-success">{$profile.nb_products}</span></span>
                            <span class="col-xs-1 text-right" style="position: relative; float: left; padding-left: 5px; padding-right: 5px; min-height: 1px; width: 70px;"><span class="badge badge-danger">{$profile.count_product_errors}</span></span>
                            <span class="col-xs-1 text-right" style="position: relative; float: left; padding-left: 5px; padding-right: 5px; min-height: 1px; width: 70px;"><span class="badge badge-danger">{$profile.count_order_errors}</span></span>
                            <span class="col-xs-1 text-right" style="position: relative; float: left; padding-left: 5px; padding-right: 5px; min-height: 1px; width: 70px;"><span class="badge badge-primary">{$profile.nb_tasks}</span></span>
                        {/if}
                    </a>
                </li>
            {/foreach}

            </ul>
        </div>
    </div>
{/if}


<script>

    // Select item from user dropdown
    $(document).on('click', '.js-user-dropdown .dropdown-menu li a', function() {
        var url_ebay = '{$url_ebay}';
        $(location).attr('href', url_ebay + '&ebay_profile=' + $(this).data('value'));
    });
    {if !$syncProductByCron}
    $(document).ready(function(){
        var cron_url = '{$cron_url}';
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: cron_url,
            success: function (data) {
                // @TODO put something here ?
            }
        });
    });
    {/if}

    $(document).ready(function () {
        var $dropdown = $('.dropdown-menu-marker '),
            width = $dropdown.width(),
            windowWidth = $(window).width(),
            $ebayBtn = $('.totebay button.dropdown-toggle'),
            ebayBtnLeftOffset = $ebayBtn.offset().left;

        if (ebayBtnLeftOffset + width > windowWidth) {
            $dropdown.addClass('dropdown-menu-right');
            $dropdown.removeClass('dropdown-menu-left');
        } else {
            $dropdown.removeClass('dropdown-menu-left');
            $dropdown.addClass('dropdown-menu-right');
        }
    });

    /* $('.ebay_profils').change(function() {
     $('.change_profile').find("input[name=ebay_profile]").val($( ".ebay_profils option:selected").attr('id'));
     $('.change_profile').submit();
     });*/
</script>