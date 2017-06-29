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



<div>
	<table class="table tableDnD" cellpadding="0" cellspacing="0" width="90%">
		<thead>
		<tr class="nodrag nodrop">
			<th>{l s='ID' mod='ebay'}</th>
			<th>{l s='Date' mod='ebay'}</th>
			<th>{l s='ID Product' mod='ebay'}</th>
			<th>{l s='ID Attribute' mod='ebay'}</th>
		</tr>
		</thead>
		<tbody>
		{if $tasks}
			{foreach from=$tasks item='task'}
				<tr>
					<td>{$task.id|escape:'htmlall':'UTF-8'}</td>
					<td>{$task.date_add|escape:'htmlall':'UTF-8'}</td>
					<td>{$task.id_product|escape:'htmlall':'UTF-8'}</td>
					<td>{$task.id_product_attribute|escape:'htmlall':'UTF-8'}</td>
				</tr>
			{/foreach}
		{/if}
		<tr></tr>
		</tbody>
	</table>
</div>

