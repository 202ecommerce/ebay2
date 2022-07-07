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

<label for="" class="control-label col-md-6">
    Business Policies* :
</label>
<select name="return_policies" style="width: 200px;">
    {if empty($RETURN_POLICY)}
        <option disabled="disabled"  value="">{l s='Please create a return policy in the Sell section of My eBay' mod='ebay'}</option>
    {else}
        <option  value=""></option>
    {/if}
    {foreach from=$RETURN_POLICY item=RETURN}
        <option value="{$RETURN.id_bussines_Policie}" >{$RETURN.name}</option>
    {/foreach}
</select>

<label for="" class="control-label col-md-6">
    Business Policies* :
</label>
<select name="payement_policies" style="width: 200px;">
    {if empty($PAYEMENTS)}
        <option disabled="disabled" value="">{l s='Please create a payment policy in the Sell section of My eBay' mod='ebay'}</option>
    {else}
        <option  value=""></option>
    {/if}

    {foreach from=$PAYEMENTS item=PAYEMENT}
        <option  value="{$PAYEMENT.id_bussines_Policie}">{$PAYEMENT.name}</option>
    {/foreach}
</select>