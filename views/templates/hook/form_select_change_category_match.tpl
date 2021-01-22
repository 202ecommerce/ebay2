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

<div class="select-holder">
  <select name="category[{$id_category}]" id="categoryLevel{$level + 1}-{$id_category}" rel="{$id_category}"
          OnChange="changeCategoryMatch({$level + 1}, {$id_category});">
    <option value="0">{$ch_cat_str}</option>;
    {foreach from=$ebay_category_list_level item="ebay_category"}
      <option value="{$ebay_category['id_ebay_category']}"{if $select && $select == $ebay_category.id_ebay_category}selected="selected"{/if}>{$ebay_category.name}{if $ebay_category.is_multi_sku == 1}*{/if}</option>
    {/foreach}
  </select>
</div>