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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


<form action="{$url|escape:'htmlall':'UTF-8'}" method="post" class="form form-horizontal panel" >
	<fieldset>
		<div class="panel-heading">{l s='Order Synchronization from PrestaShop to eBay' mod='ebay'}</div>

		<div class="form-group">
			<div class="col-sm-9 col-sm-push-3">
				<div class="checkbox">
					<label for="send_tracking_code">
						<input type="checkbox" id="send_tracking_code" name="send_tracking_code" value="1"{if $send_tracking_code} checked="checked"{/if}>
						{l s='Send tracking code' mod='ebay'}
					</label>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Status used to indicate product has been shipped' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<select name="shipped_order_state" class="ebay_select" >
					<option value=""></option>
					{if isset($order_states) && $order_states && sizeof($order_states)}
						{foreach from=$order_states item='order_state'}
							<option value="{$order_state.id_order_state|escape:'htmlall':'UTF-8'}"{if $order_state.id_order_state == $current_order_state} selected{/if}>{$order_state.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					{/if}
				</select>
			</div>
		</div>

		{if $help_ean.ps_version > '1.4.11'}
			<div class="form-group">
				<label class="control-label col-sm-3">
					<span class="label-tooltip" title="{l s='Synchronisation is 2 ways : a cancellation validated on eBay will change PrestaShop order to this status, and a change to this status on PrestaShop will cancel order on eBay.' mod='ebay'}">
						{l s='Status used to indicate order has been canceled' mod='ebay'}
					</span>
				</label>
				<div class="col-sm-9">
					<select name="return_order_state" class="ebay_select" data-inlinehelp="{l s='Synchronisation is 2 ways : a cancellation validated on eBay will change PrestaShop order to this status, and a change to this status on PrestaShop will cancel order on eBay.' mod='ebay'}">
						<option value=""></option>
						{if isset($order_states) && $order_states && sizeof($order_states)}
							{foreach from=$order_states item='order_state'}
								<option value="{$order_state.id_order_state|escape:'htmlall':'UTF-8'}"{if $order_state.id_order_state == $current_order_return_state} selected{/if}>{$order_state.name|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						{/if}
					</select>
				</div>
			</div>
		{/if}
	</fieldset>
	<fieldset style="margin-top:10px;">
		<div class="panel-heading">{l s='Orders Collection Duration' mod='ebay'}</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Since when fetch orders' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<input type="number" class="form-control width-num" name="orders_days_backward" min ="0" max="14" value="{$orders_days_backward|escape:'htmlall':'UTF-8'}">
			</div>
			<div class="col-sm-9 col-sm-push-3">
				<div class="help-block">
					{l s='In days, change if you receive more than 100 orders per fortnight.' mod='ebay'}
				</div>
			</div>
		</div>
	</fieldset>

	<div class="panel-footer" id="buttonEbayParameters">
		<a href="#categoriesProgression">
			<input class="primary button" name="submitSave" type="hidden" value="{l s='Save and continue' mod='ebay'}" />
			<button class="btn btn-default pull-right" type="submit" id="save_ebay_parameters_orders">
				<i class="process-icon-save"></i>
				{l s='Save' mod='ebay'}
			</button>
		</a>
	</div>

</form>
