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
<?xml version="1.0" encoding="utf-8"?>
<GetCategories xmlns="urn:ebay:apis:eBLBaseComponents">
    <Version>{$version|escape:'htmlall':'UTF-8'}</Version>
    <RequesterCredentials>
        <eBayAuthToken>{$ebay_auth_token|escape:'htmlall':'UTF-8'}</eBayAuthToken>
    </RequesterCredentials>
    <CategorySiteID>{$category_site_id|escape:'htmlall':'UTF-8'}</CategorySiteID>
    <DetailLevel>ReturnAll</DetailLevel>
    {if $root == true && $all == false}
        <LevelLimit>1</LevelLimit>
    {elseif $root == false && $all == true}
        <LevelLimit>5</LevelLimit>
    {else}
        <LevelLimit>5</LevelLimit>
        <CategoryParent>{$id_category|escape:'htmlall':'UTF-8'}</CategoryParent>
    {/if}
    <ViewAllNodes>true</ViewAllNodes>
</GetCategories>

