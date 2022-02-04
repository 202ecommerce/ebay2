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


<button id="onboarding-btn" class="btn btn-default" type="button">
    {l s='Generate OAuth token' mod='ebay'}
</button>

<button id="onboarding-conf-btn" class="btn btn-default" type="button">
    {l s='Configure Onboarding' mod='ebay'}
</button>

<button id="refresh-token-btn" class="btn btn-default" type="button">
    {l s='Refresh Token' mod='ebay'}
</button>

<div id="onboarding-modal" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div style="padding: 20px">
                <p>{l s='Pay attention you should set right \'Your auth accepted URL\'' mod='ebay'}</p>
                <p>{l s='Right URL' mod='ebay'}: <b>{Context::getContext()->link->getAdminLink('AdminTokenListener')}</b></p>

            </div>

            <div class="form-group">
                <label class="control-label col-sm-3">
                    {l s='APP ID' mod='ebay'}
                </label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="EBAY_APP_ID" value="{if isset($appId)}{$appId}{/if}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-3">
                    {l s='CERT ID' mod='ebay'}
                </label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="EBAY_CERT_ID" value="{if isset($certId)}{$certId}{/if}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-3">
                    {l s='RuName (eBay Redirect URL name)' mod='ebay'}
                </label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="EBAY_RU_NAME" value="{if isset($ruName)}{$ruName}{/if}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-3">
                    {l s='Onboarding URL' mod='ebay'}
                </label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="EBAY_ONBOARDING_URL" value="{if isset($onboardingUrl)}{$onboardingUrl}{/if}">
                </div>
            </div>

            <div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='ebay'}</button>
                    <button type="button" class="btn btn-default" save-onboarding-url-btn>{l s='Save' mod='ebay'}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{Context::getContext()->shop->getBaseURL(true)}modules/ebay/views/js/ButtonOnboarding.js"></script>
<script>
    var buttonOnboardingObj = new ButtonOnboarding({
        modal: document.getElementById('onboarding-modal'),
        btn: document.getElementById('onboarding-btn'),
        confBtn: document.getElementById('onboarding-conf-btn'),
        refreshBtn: document.getElementById('refresh-token-btn'),
        controller: '{$formEbaySyncController}'
    });
    buttonOnboardingObj.init();
</script>