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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<style>
	.pagination span.current{
		color: white !important;
		background:#00aff0 !important;
	}
	.pagination li{
		width:auto;
		margin:0;
		height:auto;
	}
</style>

<ul class="pagination">
	<li>
		<span value="1"> << </span>
	</li>
	<li>
		<span {if $page_current != 1}value="{$prev_page}"{/if}> < </span>
	</li>
	{if $start>1}
		<li>
			<span>...</span>
		</li>
	{/if}
	{for $page=$start to $stop}
		{if $page==$page_current}
			<li>
				<span class="current" value="{$page}">{$page}</span>
			</li>
		{else}
			<li>
				<span value="{$page}">{$page}</span>
			</li>
		{/if}
	{/for}
    {if $stop != $pages_all}
		<li>
			<span>...</span>
		</li>
    {/if}
	<li>
		<span {if $page_current != $pages_all} value="{$next_page}"{/if}> > </span>
	</li>
	<li>
		<span value="{$pages_all}"> >> </span>
	</li>
</ul>