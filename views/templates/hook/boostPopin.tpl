{*
* 2007-2021 PrestaShop
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
* @copyright Copyright (c) 2007-2021 202-ecommerce
* @license Commercial license
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="popin-mode_boost" class="popin popin-lg" style="display: none;">
    <div class="panel">
        <div  class="panel-heading">
            <i class="icon-rocket"></i> {l s='Mode Boost' mod='ebay'}
            <span class="badge badge-success"><span class="page_popin_boost" id="1">1</span> / 2</span>
        </div>

        <form action="" class="form-horizontal" method="post">
            <div id="1" class="page_boost selected first_page_popin">
                <p>{l s='Boost mode allows for 5 tasks to be proceeded simultaneously. It\'s suited when you have a large amount of tasks.'  mod='ebay'}</p>
                <div class='alert alert-warning'>
                    <button class='close' data-dismiss='alert'>&#215;</button>
                    {l s='After launching boost mode, you must keep your browser window open.' mod='ebay'}
                </div>
            </div>
            <div id="2" class="page_boost" style="display: none">
                <p class="big">{l s='Synchronisation in progress' mod='ebay'}</p>

                <div class="progressbar">
                    <div class="tasks">
                        <strong><span class="value valueDone">0</span> / <span class="value valueMax">{l s='loading...' mod='ebay'}</span> {l s='tasks' mod='ebay'}</strong>
                        <strong class="percentages">{l s='loading...' mod='ebay'}</strong>
                    </div>
                    <div class="line-wrapper">
                        <div class="line percentages_line" style="width: 0%;"></div>
                    </div>
                </div>

                <div class='alert alert-warning'>
                    <button class='close' data-dismiss='alert'>&#215;</button>
                    {l s='You must keep your browser window open else synchronisation will stop.' mod='ebay'}
                </div>
            </div>
        </form>

        <div class="panel-footer">
            <button class="js-close-popin_boost btn btn-danger pull-right" style="display: none"><i class="process-icon-cancel"></i>{l s='Stop Boost' mod='ebay'}</button>
            <button class="js-close-popin btn btn-primary pull-right close_boost"><i class="process-icon-cancel"></i>{l s='Close' mod='ebay'}</button>
            <button class="js-next-popin_boost btn btn-primary pull-right star_boost"><i class="process-icon-next"></i>{l s='Start' mod='ebay'}</button>

        </div>
    </div>
</div>