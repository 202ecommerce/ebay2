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

<div class="warning big">
    {l s='Only cancellation, refund and returns validated on eBay will be imported to PrestaShop.' mod='ebay'}
</div>

<fieldset style="margin-top:10px;">
    <legend>{l s='Synchronize' mod='ebay'}</span></legend>

    <label>
        {l s='Manually Sync refunds and returns' mod='ebay'}
    </label>

    <div class="margin-form">
        <a href="{$url|escape:'htmlall':'UTF-8'}&EBAY_SYNC_ORDERS_RETURNS=1">
            <input type="button" class="button" value="{l s='Sync refunds and returns from eBay' mod='ebay'}"/>
        </a>
    </div>
</fieldset>
<br>
<p>
    {l s='Refunds and returns are automatically retrieved from eBay every 30 minutes. You can immediately retrieve them by clicking the button below.' mod='ebay'}
</p>
<p>
    {l s='If you wish to retrieve refunds and returns using a cron task, please go to the “Advanced parameters” tab.' mod='ebay'}
</p>
