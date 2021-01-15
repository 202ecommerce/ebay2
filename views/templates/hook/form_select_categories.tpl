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

{if $level > 1}
	<select name="category[{$id_category}]" id="categoryLevel{$category.level}-0" rel="{$id_category}" OnChange="changeCategoryMatch({$category.level}, {$id_category});">
		{foreach from=$ref_categories.{$category.id_category_ref_parent}.children item="child"}
			<option value="{$ref_categories.{$child}.id_ebay_category}"{if $category.id_category_ref == $child}selected{/if}>{$ref_categories.{$child}.name}</option>
		{/foreach}
	</select>
{else}
	<select name="category[{$id_category}]" id="categoryLevel{$level}-0" rel="{$id_category}" OnChange="changeCategoryMatch({$level}, {$id_category});">
		<option value="0">{$ch_cat_str}</option>
		{foreach from=$ref_categories item="category"}
			<option value="{$category.id_ebay_category}"{if $category.id_category_ref == $id_category_ref} selected{/if}>{$category.name}</option>
		{/foreach}
	</select>
{/if}
