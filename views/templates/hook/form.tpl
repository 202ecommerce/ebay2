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

<div class="totebay">
<script type="text/javascript">
    regenerate_token_show = false;
    var module_dir = "{$_module_dir_|escape:'htmlall':'UTF-8'}";
    var id_lang = {$id_lang|escape:'htmlall':'UTF-8'};
    var id_ebay_profile = {$id_ebay_profile|escape:'htmlall':'UTF-8'};
    {if $regenerate_token != false}
    regenerate_token_show = true;
    {/if}
</script>

<link rel="stylesheet" href="{$css_file|escape:'htmlall':'UTF-8'}" />
<link rel="stylesheet" href="{$font_awesome_css_file|escape:'htmlall':'UTF-8'}" />
<link rel="stylesheet" href="{$bootstrapselectcss|escape:'htmlall':'UTF-8'}" />
<script>
    var $j = $;
</script>
{if substr($smarty.const._PS_VERSION_, 0, 3) == "1.4" || substr($smarty.const._PS_VERSION_, 0, 5) == "1.5.2"}
    <link rel="stylesheet" href="{$fancyboxCss|escape:'htmlall':'UTF-8'}" />
    <script src="{$ebayjquery|escape:'htmlall':'UTF-8'}"></script>
    <script src="{$noConflicts|escape:'htmlall':'UTF-8'}"></script>
    <script>
        if(typeof($j172) != 'undefined')
            $j = $j172;
        else
            $j = $;
    </script>
    <script src="{$fancybox|escape:'htmlall':'UTF-8'}"></script>
{/if}
<script src="{$bootstrapselect|escape:'htmlall':'UTF-8'}"></script>
<script src="{$tooltip|escape:'htmlall':'UTF-8'}" type="text/javascript"></script>
<script src="{$tips202|escape:'htmlall':'UTF-8'}" type="text/javascript"></script>

{literal}

{/literal}

{if $show_welcome}
<div class="ebay-welcome">
    <img id="ebay-logo" src="{$path|escape:'htmlall':'UTF-8'}views/img/ebay.png" />
    <div id="ebay-welcome-top" class="ebay-boxes-2-col-table">
        <div class="ebay-boxes-2-col-cell right">
            <div class="ebay-boxes-2-col-cell-content">
                <div id="ebay-welcome-left-content">
                    <p class="title ebay-title">{l s='A PERFECT PARTNER FOR YOUR BUSINESS' mod='ebay'}</p>
                    <p>{{l s='eBay is one of the |b|largest marketplaces in the world that connects buyers and sellers of all sizes around the world|/b|.' mod='ebay'}|replace:'|b|':'<b>'|replace:'|/b|':'</b>'}</p>

                    <p>{l s='eBay represents a great opportunity for you to reach millions of new customers and help you to  grow your business.' mod='ebay'}</p>

                    <p><b>{l s='With the eBay add-on for PrestaShop you can:' mod='ebay'}</b></p>
                    <ul class="ebay-bullet-points">
                        <li>{l s='Export your products from your PrestaShop shop to eBay.' mod='ebay'}</li>
                        <li>{l s='Manage both channels from your back-office.' mod='ebay'}</li>
                        <li>{l s='Develop a profitable additional sales channel.' mod='ebay'}</li>
                    </ul>
                    <p><b>{l s='Start selling now with the PrestaShop FREE add-on for eBay!' mod='ebay'}</b></p>
                </div>
            </div>
        </div>
        <div id="ebay-welcome-right" class="ebay-boxes-2-col-cell">
            <div class="ebay-boxes-2-col-cell-content right">
                <div class="ebay-light-gray-box">
                    <div class="ebay-light-gray-box-content-outer">
                        <div class="ebay-light-gray-box-content">
                            <p class="ebay-light-gray-box-title">{l s='Start selling on eBay with PrestaShop is easy:' mod='ebay'}</p>
                            <ul class="ebay-light-gray-box-ul">
                                <li><span class="ebay-light-gray-box-number">1</span> {l s='Create an eBay business account' mod='ebay'}</li>
                                <li><span class="ebay-light-gray-box-number">2</span> {l s='Open your eBay shop' mod='ebay'}</li>
                                <li><span class="ebay-light-gray-box-number">3</span> {l s='Link your eBay account to the eBay add-on' mod='ebay'}</li>
                                <li><span class="ebay-light-gray-box-number">4</span> {l s='Configure the eBay add-on' mod='ebay'}</li>
                            </ul>
                        </div>
                    </div>
                    {if $free_shop_for_90_days}
                    <div class="ebay-dark-gray-box-container">
                        <p class="ebay-dark-gray-box-title">
                            {l s='Open your shop for free' mod='ebay'}
                        </p>
                        <div class="ebay-dark-gray-box-content">
                            {l s='Begin or boost your activity on eBay, enjoying ' mod='ebay'}<b> {l s='90 free days of subscription' mod='ebay'}</b> {l s='to eBay Shops' mod='ebay'}<br/>
                            <a href="{l s='http://pages.ebay.fr/promos/termsfrstoretrial3months/' mod='ebay'}" target="_blank">{l s='More informations' mod='ebay'}</a>
                        </div>
                        <img src="../modules/ebay/views/img/open_your_shop.jpg" alt="" id="open_your_shop">
                        <div class="clear clearfix"></div>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
        <div style="clear:both"></div>
    </div>
</div>
{/if}


                {if $show_welcome}
                    <legend><img src="{$path|escape:'htmlall':'UTF-8'}logo.gif" alt="" />{l s='EBay Module Status' mod='ebay'}

                    </legend>

                    <div>
                        {if empty($alert)}
                            <p id="ebay-no-profile">{l s='You don\'t have any profile setup yet' mod='ebay'}</p>
                            {if $is_version_one_dot_five_dot_one}
                                <br/><img src="../modules/ebay/views/img/warn.png" /><strong>{l s='You\'re using version 1.5.1 of PrestaShop. We invite you to upgrade to version 1.5.2  so you can use the eBay module properly.' mod='ebay'}</strong>
                                <br/><strong>{l s='Please synchronize your eBay sales in your Prestashop front office' mod='ebay'}</strong>
                            {/if}
                        {else}
                            <img src="../modules/ebay/views/img/warn.png" /><strong>{l s='Please complete the following settings to configure the module' mod='ebay'}</strong>
                            <br />{if in_array('registration', $alert)}<img src="../modules/ebay/views/img/warn.png" />{else}<img src="../modules/ebay/views/img/valid.png" />{/if} 1) {l s='Register the module on eBay' mod='ebay'}
                            <br />{if in_array('allowurlfopen', $alert)}<img src="../modules/ebay/views/img/warn.png" />{else}<img src="../modules/ebay/views/img/valid.png" />{/if} 2) {l s='Allow url fopen' mod='ebay'}
                            <br />{if in_array('curl', $alert)}<img src="../modules/ebay/views/img/warn.png" />{else}<img src="../modules/ebay/views/img/valid.png" />{/if} 3) {l s='Enable cURL' mod='ebay'}
                            <br />{if in_array('SellerBusinessType', $alert)}<img src="../modules/ebay/views/img/warn.png" />{else}<img src="../modules/ebay/views/img/valid.png" />{/if} 4) {l s='Please register an eBay business seller account to configure the application' mod='ebay'}
                        {/if}
                    </div>
                {else}
                        {if $profiles && count($profiles)}

                    {*<div class="panel-heading">
                         <img src="{if isset($_path) && !empty($_path)}{$_path|escape:'htmlall':'UTF-8'}{elseif isset($path)}{$path|escape:'htmlall':'UTF-8'}{/if}logo.gif" alt="" />{l s='eBay Profiles' mod='ebay'}
                             {if $debug == 1}
                                 <small  style="background-color: #F80; color: #FFF; padding:5px;">in SANDBOX mode  !!</small>
                             {/if}

                    </div>*}
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="panel clearfix panel-info">
                                        <form class="form-horizontal" role="form">
                                            <label class="control-label pull-left">
                                                {l s='Profiles' mod='ebay'} :
                                            </label>

                            {* Custom dropdown with table inside *}
                            <div class="pull-left">
                                <div class="dropdown js-user-dropdown">
                                    <button class="form-control dropdown-toggle" type="button" data-toggle="dropdown">
                                        {$current_profile->ebay_user_identifier}<span class="caret"></span>
                                    </button>
                                    <div class="dropdown-menu profile-beautify-wrapper">
                                        <table class="profiles-beautify">
                                            <tr class="head">
                                                <td><span class="nowrap">{l s='User' mod='ebay'}</span></td>
                                                <td><span class="nowrap">{l s='Site ebay' mod='ebay'}</span></td>
                                                <td><small>{l s='Listings' mod='ebay'}</small></td>
                                                <td><small>{l s='Listing errors' mod='ebay'}</small></td>
                                                <td><small>{l s='Order errors' mod='ebay'}</small></td>
                                                <td><small>{l s='Tasks' mod='ebay'}</small></td>
                                            </tr>

                                            {foreach from=$profiles item=profile}
                                                <tr>
                                                    <td class="{if $current_profile->id == $profile.id_ebay_profile}selected{/if}" href="#" data-value="{$profile.id_ebay_profile|escape:'htmlall':'UTF-8'}">
                                                        <a class="{if $current_profile->id == $profile.id_ebay_profile}selected{/if}" href="#" data-value="{$profile.id_ebay_profile|escape:'htmlall':'UTF-8'}">
                                                            <span id = 'name'>
                                                                {if $profile['categoryIsLoaded']}
                                                                    {$profile.ebay_user_identifier|escape:'htmlall':'UTF-8'}
                                                                {else}
                                                                    {l s='New' mod='ebay'}
                                                                {/if}
                                                            </span>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a class="{if $current_profile->id == $profile.id_ebay_profile}selected{/if}" href="#" data-value="{$profile.id_ebay_profile|escape:'htmlall':'UTF-8'}">
                                                            <span>{$profile.site_name|escape:'htmlall':'UTF-8'}</span>
                                                        </a>
                                                    </td>
                                                    {if $profile.token == 0 }
                                                        <td colspan="4" class="text-center">
                                                            <a class="{if $current_profile->id == $profile.id_ebay_profile}selected{/if}" href="#" data-value="{$profile.id_ebay_profile|escape:'htmlall':'UTF-8'}">
                                                                <span class="text-right">{l s='Not associated with Ebay' mod='ebay'}</span>
                                                            </a>
                                                        </td>
                                                    {elseif $profile['categoryIsLoaded'] == false}
                                                        <td colspan="4" class="text-center">
                                                            <a class="{if $current_profile->id == $profile.id_ebay_profile}selected{/if}" href="#" data-value="{$profile.id_ebay_profile|escape:'htmlall':'UTF-8'}">
                                                                <span class="text-right">{l s='In process' mod='ebay'}</span>
                                                            </a>
                                                        </td>
                                                    {elseif $profile.category == 0}
                                                        <td colspan="4" class="text-center">
                                                            <a class="{if $current_profile->id == $profile.id_ebay_profile}selected{/if}" href="#" data-value="{$profile.id_ebay_profile|escape:'htmlall':'UTF-8'}">
                                                                <span class="text-right">{l s='No category configured' mod='ebay'}</span>
                                                            </a>
                                                        </td>
                                                    {else}
                                                        <td class="text-center">
                                                            <a class="{if $current_profile->id == $profile.id_ebay_profile}selected{/if}" href="#" data-value="{$profile.id_ebay_profile|escape:'htmlall':'UTF-8'}">
                                                            <span class="badge badge-success">{$profile.nb_products}</span>

                                                            </a>
                                                        </td>
                                                        <td class="text-center">
                                                            <a class="{if $current_profile->id == $profile.id_ebay_profile}selected{/if}" href="#" data-value="{$profile.id_ebay_profile|escape:'htmlall':'UTF-8'}">
                                                            <span class="badge badge-danger">{$profile.count_product_errors}</span>

                                                            </a>
                                                        </td>
                                                        <td class="text-center">
                                                            <a class="{if $current_profile->id == $profile.id_ebay_profile}selected{/if}" href="#" data-value="{$profile.id_ebay_profile|escape:'htmlall':'UTF-8'}">
                                                            <span class="badge badge-danger">{$profile.count_order_errors}</span>
                                                            </a>

                                                        </td>
                                                        <td class="text-center">
                                                            <a class="{if $current_profile->id == $profile.id_ebay_profile}selected{/if}" href="#" data-value="{$profile.id_ebay_profile|escape:'htmlall':'UTF-8'}">
                                                                <span class="badge badge-primary">{$profile.nb_tasks}</span>
                                                            </a>
                                                        </td>
                                                    {/if}
                                                </tr>
                                            {/foreach}

                                        </table>
                                    </div>
                                </div>
                            </div>

                            <a href="{$add_profile_url|escape:'htmlall':'UTF-8'}">
                                <button type="button" class="btn btn-default pull-left" title="{l s='Add profile' mod='ebay'}">
                                    <i class="icon-plus"></i>
                                </button>
                            </a>

                            <button type="button" class="btn btn-default btn-hover-danger pull-left delete-profile" title="{l s='Remove profile' mod='ebay'}" href="#popin-remove-profile" data-profile="{$current_profile->id}">
                                <i class="icon-trash"></i>
                            </button>
                            {if $debug == 1}
                                <small  style="background-color: #fbbb22; color: #FFF; padding: 0 12px; display: inline-block; height: 31px; line-height: 31px; border-radius: 3px;">{l s='in SANDBOX mode !' mod='ebay'}</small>
                            {/if}

                            <label class="nb_tasks_in_work refreshNbTasksInWork btn btn-md {if $task_total_todo < 500}btn-success {else}btn-warning{/if} pull-left" style="display: none">
                                <strong class="nb_tasks_in_work_amount">0</strong> {l s='tasks left' mod='ebay'}
                                <i class="icon-circle-o-notch icon-spin"></i>
                            </label>

                            <label class="nb_tasks_in_work_success refreshNbTasksInWork btn btn-md btn-success pull-left" style="display: none">
                                {l s='Last SYNC :' mod='ebay'}
                            {if $last_sync_prod == ''}
                                {l s='never done' mod='ebay'}
                            {else}
                                {$last_sync_prod}
                            {/if}
                            </label>


                                <label class="mode_boost pull-left">
                                    <a href="#popin-mode_boost" class="js-popin btn btn-md btn-default">{l s='Boost' mod='ebay'} </a>
                                </label>

                                <label class="warning-message nb_tasks_in_work_message pull-left" {if $task_total_todo < 500}style="display: none;"{/if}>
                                    <small>{l s='You have a large amount of task to proceed.' mod='ebay'}</small><br>
                                    <small>{l s='You may want to use boost mode.' mod='ebay'}</small>
                                </label>

                                            <div class="links-wrapper">
                                                <div class="pull-right">
                                                    <a href="https://desk.202-ecommerce.com/portal/en/kb/addons-prestashop-help-center/ebay-2-official" target="_blank">
                                                        <i class="icon-question-circle"></i>
                                                        <span>{l s='Manual' mod='ebay'}</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div id="popin-container-help"></div>
                                        </form>
                                        <form class="change_profile" method="post">
                                            <input type="hidden" name="ebay_profile" value="" />
                                            <input type="hidden" name="action" value="logged" />
                                        </form>
                                    </div>
                                </div>
                            </div>

                        {else}
                            <legend><img src="{$path|escape:'htmlall':'UTF-8'}logo.gif" alt="" />{l s='Status of your eBay Add-on' mod='ebay'}</legend>
                            <p id="ebay-no-profile">{l s='You don\'t have any profile setup yet' mod='ebay'}</p>
                        {/if}
                    {/if}

<div style="clear:both"></div>

<!-- seller tips -->
{if $show_seller_tips}
    <div id="seller-tips" class="ebay-welcome" style="display:none">
        <div id="ebay-tips" class="ebay-boxes-2-col-table">
            <div class="ebay-boxes-2-col-cell right">
                <div class="ebay-boxes-2-col-cell-content">
                    <div id="ebay-welcome-left-content" style="padding-bottom: 3em">
                        <img id="ebay-logo" src="../modules/ebay/views/img/ebay.png" />
                        <p class="title ebay-title">{l s='A PERFECT PARTNER FOR YOUR BUSINESS' mod='ebay'}</p>
                        <p>{{l s='eBay is one of the |b|largest marketplaces in the world that connects buyers and sellers of all sizes around the world|/b|.' mod='ebay'}|replace:'|b|':'<b>'|replace:'|/b|':'</b>'}</p>

                        <p>{l s='eBay represents a great opportunity for you to reach millions of new customers and help you to  grow your business.' mod='ebay'}</p>
                    </div>
                </div>
            </div>
            <div id="ebay-welcome-right" class="ebay-boxes-2-col-cell">
                <div class="ebay-boxes-2-col-cell-content right">
                    <div class="ebay-light-gray-box">
                        <div class="ebay-light-gray-box-content-outer">
                            <div class="ebay-light-gray-box-content">
                                <p class="ebay-light-gray-box-title">{l s='Tips to sell more on eBay:' mod='ebay'}</p>
                                <ul class="ebay-light-gray-box-ul">
                                    <li>
                                        {assign var="link" value="<a href=\"{$title_desc_url|escape:'htmlall':'UTF-8'}\" target=\"_blank\">"}
                                        <span class="ebay-light-gray-box-number">1</span> <b>{l s='Help buyers to find your product:' mod='ebay'}</b> {{l s='Write good |a|titles and descriptions|/a|' mod='ebay'}|replace:'|a|':$link|replace:'|/a|':'</a>'}</a>
                                    </li>
                                    <li>
                                        {assign var="link" value="<a href=\"{$similar_items_url|escape:'htmlall':'UTF-8'}\" target=\"_blank\">"}
                                        <span class="ebay-light-gray-box-number">2</span> <b>{l s='Make your products competitive:' mod='ebay'}</b> {{l s='|a|research on eBay for similar products|/a| to yours and compare with your prices.' mod='ebay'}|replace:'|a|':$link|replace:'|/a|':'</a>'}
                                    </li>
                                    <li>
                                        {assign var="link" value="<a href=\"{$picture_url|escape:'htmlall':'UTF-8'}\" target=\"_blank\">"}
                                        <span class="ebay-light-gray-box-number">3</span> {{l s='|b|Take special care of your product pictures,|/b| |a|this will help buyers to buy from you.|/a|' mod='ebay'}|replace:'|b|':'</b>'|replace:'|/b|':'</b>'|replace:'|a|':$link|replace:'|/a|':'</a>'}
                                    </li>
                                    <li>
                                        {assign var="link" value="<a href=\"{$top_rated_url|escape:'htmlall':'UTF-8'}\" target=\"_blank\">"}
                                        <span class="ebay-light-gray-box-number">4</span> {{l s='|b|Make buyers to come back|/b| by |a|delivering a great service|/a| and offering free shipping.' mod='ebay'}|replace:'|b|':'</b>'|replace:'|/b|':'</b>'|replace:'|a|':$link|replace:'|/a|':'</a>'}                                                        </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="clear:both"></div>
        </div>
    </div>

    {if $current_profile && !$add_profile}
<div class="row">
    {* Main navigation *}
    <div class="sidebar navigation col-md-3 col-lg-2">
        <ul class="list-group nav">
            <a id="dashboard-menu-link" class="list-group-item main-menu-a" data-toggle="tab" href="#dashboard" data-sub="dashboard" ><i class="icon-tachometer"></i> {l s='Dashboard' mod='ebay'}</a>
            <a id="orders-menu-link" class="list-group-item main-menu-a" data-toggle="tab" href="#orders" data-sub="orders"><i class="icon-share-square"></i> {l s='Orders' mod='ebay'} {if $count_order_errors > 0}<span class="badge badge-danger">{$count_order_errors}</span>{/if}</a>
            <a id="annonces-menu-link" class="list-group-item active main-menu-a" data-toggle="tab" href="#annonces" data-sub="annonces"><i class="icon-list-alt"></i> {l s='Listings' mod='ebay'} {if $count_product_errors_total > 0}<span class="badge badge-danger">{$count_product_errors_total}</span>{/if}</a>
            <a id="settings-menu-link" class="list-group-item main-menu-a" data-toggle="tab" href="#settings" data-sub="settings"><i class="icon-cog"></i> {l s='Settings' mod='ebay'}</a>
            <a id="log-menu-link" class="list-group-item main-menu-a" data-toggle="tab" href="#log" data-sub="log"><i class="icon-file-text"></i> {l s='Log' mod='ebay'}</a>
        </ul>
    </div>

    {/if}
{/if}
    <div id="popin_load_category-container">
        <div  class="popin popin-lg" id="categoriesEbayProgression" style="display: none; height: 500px;">
            <button class="btn btn-default" id="exit_load_cat">{l s='Close' mod='ebay'}</button>
            <table id="load_cat_ebay" class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%;">
                <thead>
                <tr class="nodrag nodrop">
                    <th style="width:10%">
                        {l s='Status' mod='ebay'}
                    </th>
                    <th style="width:45%">
                        {l s='Description' mod='ebay'}
                    </th>
                    <th style="width:45%">
                        {l s='Result' mod='ebay'}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr id="cat_parent" class="load">
                    <td></td>
                    <td>{l s='Loading list of eBay categories' mod='ebay'}</td>
                    <td>{l s='In progress' mod='ebay'}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id="popin_config_ok"">
        <div  class="popin popin-lg" id="config_ok" style="display: none;text-align: center;padding: 15px;">
            <p>{l s='Module configuration is done, you now have to choose products to be sent to eBay in Listing tab.' mod='ebay'}</p>
            <div class="panel-footer">
                <button class="close_popin_config btn btn-default">OK</button>
            </div>
        </div>
    </div>
    <a href="#config_ok" class="popin_config_ok_sync" style="display: none"></a>

    <div id="popin-delete-profile" class="popin popin-sm" style="display: none;position: fixed;z-index: 1;left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgb(0,0,0);background-color: rgba(0,0,0,0.4);">
        <div class="panel" style=" background-color: #fefefe;padding: 20px;border: 1px solid #888;width: 80%;margin: 30% auto;">
            <div class="panel-heading">
                <i class="icon-trash"></i> {l s='Profile' mod='ebay'}
            </div>
            <p>{l s='Are you sure you want to delete the profile number ' mod='ebay'}<span class="id_profile"></span>?</p>

            <div class="panel-footer" style="display: flex; justify-content: space-between; align-items: center">
                <button class="cancel-delete btn btn-default"><i class="process-icon-cancel"></i>Annuler</button>
                <button class="ok-delete btn btn-success pull-right" style="padding:15px">OK</button>
            </div>
        </div>
    </div>
    {include file="./boostPopin.tpl"}
    {if $ebay_shipping_config_tab > 0 && $count_category == 0}
    <script>
        $(document).ready(function() {
            $('.popin_config_ok_sync').fancybox({
                'modal': true,
                'showCloseButton': false,
                'padding': 0,
                'parent': '#popin_config_ok',
            });
            $('.popin_config_ok_sync').click();

            $(document).on('click', '.close_popin_config', function() {
                $.fancybox.close();
            });

        });
    </script>
    {/if}
    <script type="text/javascript">
        var formEbaySyncController = "{$formEbaySyncController|addslashes}";
        var formController = "{$formController|addslashes}";
        var header_ebay_l = {
          'Hide seller tips' : "{l s='Hide seller tips' mod='ebay'}",
          'Show seller tips'  : "{l s='Show seller tips' mod='ebay'}",
          'Are you sure you want to delete the profile number %profile_number%?' : "{l s='Are you sure you want to delete the profile number %profile_number%?' mod='ebay'}",
            'Are you sure you want to exclure this product?' : "{l s='Are you sure you want to exclure this product?' mod='ebay'}",
            'Are you sure you want to inclure this product?' : "{l s='Are you sure you want to inclure this product?' mod='ebay'}",
            'Are you sure you want to delete this category?' : "{l s='Are you sure you want to delete this category?' mod='ebay'}"
        };
        var first = false;
        var boost = null;
        var main_tab = '{$main_tab}';
        var id_tab = '{$id_tab}';
        var cron_url = '{$cron_url}';
        function loadNbTasksInWork(e){
            $.ajax({
                type: 'POST',
                url : formController,
                data: {
                    ajax: true,
                    action: 'LoadNbTasksInWork',
                    id_profile: id_ebay_profile,
                },
                success : function(data) {

                    if (data != '0'){
                        first = true;
                        $('.nb_tasks_in_work').show();
                        $('.nb_tasks_in_work_success').hide();
                        $('.nb_tasks_in_work_amount').html(data);
                    } else{
                        $('.nb_tasks_in_work').hide();
                        if(first){
                            $('.nb_tasks_in_work_success').html('<i class="icon-check"></i> {l s='sync done' mod='ebay'}');
                        }
                        $('.nb_tasks_in_work_success').show();
                    }
                }
            });
        };
        boost = setInterval(function(){
            loadNbTasksInWork();
        }, 3000);

        $(document).on('click', '.refreshNbTasksInWork', loadNbTasksInWork);
    </script>
    <script>
        var module_dir = "{$_module_dir_|escape:'htmlall':'UTF-8'}";
        // Select item from user dropdown
        $(document).on('click', '.js-user-dropdown .dropdown-menu td a', function() {
            $(this).parents('.dropdown').find('.dropdown-menu td a').removeClass('selected');
            $(this).addClass('selected');
            $(this).parents('.dropdown').find('.dropdown-toggle').html($(this).find('#name').html() + ' <span class="caret"></span>');
            $(this).parents('.dropdown').find('.dropdown-toggle').val($(this).find('#name').html());
            $('.change_profile').find("input[name=ebay_profile]").val($(this).data('value'));
            $('.change_profile').submit();

        });

        $(document).ready(function() {

            $('.js-next-popin_boost').on('click', function() {

                var courant_page = $('.page_boost.selected');


                var new_id =  parseInt(courant_page.attr('id'))+1;


                    $('.page_popin_boost').html(''+new_id);
                    courant_page.removeClass('selected').hide();
                    courant_page.parent().find('div#'+new_id).addClass('selected').show();

                if (new_id == 2) {
                        $('.js-prev-popin_boost').show();
                    $('.js-next-popin_boost').hide();
                    $('.js-close-popin').show();
                }


            });
            $('.js-prev-popin_boost').on('click', function() {
                $('.js-next-popin_boost').show();
                var courant_page = $('.page_boost.selected');
                courant_page.removeClass('selected').hide();

                var new_id =  parseInt(courant_page.attr('id'))-1;
                $('.page_popin_boost').html(''+new_id);

                if(new_id == 1){
                    $('.js-prev-popin_boost').hide();
                    $('.js-close-popin').show();
                }
                $('.js-save-popin_boost').hide();

                courant_page.parent().find('div#'+new_id).addClass('selected').show();

            });

        });

       /* $('.ebay_profils').change(function() {
            $('.change_profile').find("input[name=ebay_profile]").val($( ".ebay_profils option:selected").attr('id'));
            $('.change_profile').submit();
        });*/
    </script>
    <script  type="text/javascript" src="{$_module_dir_|escape:'htmlall':'UTF-8'}ebay/views/js/form_sync_tab.js" async ></script>
    <script type="text/javascript" src="{$_module_dir_|escape:'htmlall':'UTF-8'}ebay/views/js/header.js?date={$date|escape:'htmlall':'UTF-8'}"></script>
<!-- after seller tips -->
