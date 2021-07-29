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
<div class="col-md-9 col-lg-10">
	<div class="alert alert-info">
		<div>
			{l s='eBay enabled TLS 1.2 for all APIs in Sandbox and Production. We strongly recommend that you contact your web host to verify TLS 1.2 and SHA2 compatibility.' mod='ebay'}
		</div>

		<div>
			{l s='API calls that are not TLS 1.2 compliant will fail as of 08/31/2021.' mod='ebay'}
		</div>

		<div style="margin-top: 10px">
			{include file='./tls/tls_button.tpl' controller=$formController|addslashes}
		</div>
	</div>
	{if isset($green_message) && $green_message}
		<div class="alert alert-success conf confirm settings-menu menu-msg">{$green_message|escape:'htmlall':'UTF-8'}</div>
	{/if}
	<div class="bootstrap">
		{if isset($alerts) && $alerts && sizeof($alerts)}
			{foreach from=$alerts item='alert'}
				<div class="{if $ps_version > '1.5'}alert {/if}alert-{if $alert.type == 'error'}danger{if $ps_version < '1.5'} error{/if}{elseif $alert.type == 'warning'}warning{if $ps_version < '1.5'} warn{/if}{elseif $alert.type == 'info'}info{if $ps_version < '1.5'} conf{/if}{/if}"><button type="button" class="close" data-dismiss="alert">&#215;</button>
					{if isset($alert.link_warn)}
						{assign var="link" value='<a href="'|cat:$alert.link_warn|cat:'" target="_blank">'}
						{$alert.message|regex_replace:"/@link@/":$link|regex_replace:"/@\/link@/":"</a >"}
					{else}
						{$alert.message|escape:'htmlall':'UTF-8'}
					{/if}
					{if isset($alert.kb)}
						<a class="kb-help" data-errorcode="{$alert.kb.errorcode|escape:'htmlall':'UTF-8'}" data-module="ebay" data-lang="{$alert.kb.lang|escape:'htmlall':'UTF-8'}" module_version="{$alert.kb.module_version|escape:'htmlall':'UTF-8'}" prestashop_version="{$alert.kb.prestashop_version|escape:'htmlall':'UTF-8'}">&nbsp;<i class="icon-info-circle"></i></a>
					{/if}
				</div>
			{/foreach}
		{/if}
	</div>
	<div class="panel">
		<div class="tab-content">

			<div class="tab-pane fade in active" id="annoncestab1">
				<div id='tab_title_level1' class="panel-heading"></div>

				<ul class="nav nav-pills dashboard-menu menuTab fade hidden" role="tablist">
					<li role="presentation" id="menuTab81" class="menuTabButton active" ><a href="#">{l s='Dashboard' mod='ebay'}</a></li>
				</ul>

				<ul class="nav nav-pills annonces-menu menuTab ebay_hidden " role="tablist">

					<li role="presentation" id="menuTab5" class="menuTabButton "><a href="#">{l s='List categories' mod='ebay'}</a></li>

					<li role="presentation" id="menuTab80" class="menuTabButton "><a href="#">{l s='List Errors Products' mod='ebay'}{if $count_product_errors > 0}<span class="badge">{$count_product_errors}</span>{/if}</a></li>

					<li role="presentation" id="menuTab9" class="menuTabButton"><a href="#">{l s='Ebay Listings' mod='ebay'}</a></li>
					<li role="presentation" id="menuTab16" class="menuTabButton"><a href="#">{l s='Orphan Listings' mod='ebay'}<span class="badge orhan_badge">{if $count_orphan_listing > 0}{$count_orphan_listing}{/if}</span></a></li>
					<li role="presentation" id="menuTab106" class="menuTabButton"><a href="#">{l s='Excluded products' mod='ebay'}</a></li>
				</ul>

				<ul class=" nav nav-pills orders-menu menuTab ebay_hidden" role="tablist">
					<li  role="presentation" id="menuTab6" class="menuTabButton"><a href="#">{l s='Order history' mod='ebay'}{if $count_order_errors > 0}<span class="badge badge-danger">{$count_order_errors}</span>{/if}</a></li>

					<li role="presentation" id="menuTab78" class="menuTabButton"><a href="#">{l s='Order Returns' mod='ebay'}</a></li>
					{if isset($best_offers)}
						<li role="presentation" id="menuTab999" class="menuTabButton"><a href="#">{l s='Best Offers' mod='ebay'}</a></li>
					{/if}
				</ul>


				<ul class="nav nav-pills settings-menu menuTab ebay_hidden " role="tablist">
					<li role="presentation" id="menuTab1" class="menuTabButton"><a href="#">1. {l s='General settings' mod='ebay'}</a></li>
					<li role="presentation" id="menuTab101" class="menuTabButton"><a href="#">2. {l s='Listing settings' mod='ebay'}</a></li>
					<li role="presentation" id="menuTab102" class="menuTabButton"><a href="#">3. {l s='Orders settings' mod='ebay'}</a></li>
					<li role="presentation" id="menuTab3" class="menuTabButton"><a href="#">4. {l s='Dispatch and Shipping' mod='ebay'}</a></li>
					<li role="presentation" id="menuTab103" class="menuTabButton"><a href="#">5. {l s='Internation Shipping' mod='ebay'}</a></li>
					<li role="presentation" id="menuTab4" class="menuTabButton"><a href="#">6. {l s='Template manager' mod='ebay'}</a></li>
					<li role="presentation" id="menuTab13" class="menuTabButton"><a href="#">7. {l s='Tools' mod='ebay'}</a></li>
				</ul>

				<ul class="nav nav-pills log-menu menuTab ebay_hidden " role="tablist">
					<li role="presentation" id="menuTab17" class="menuTabButton"><a href="#">1. {l s='Jobs to be done' mod='ebay'}</a></li>
					<li role="presentation" id="menuTab18" class="menuTabButton"><a href="#">2. {l s='Workers' mod='ebay'}</a></li>
					<li role="presentation" id="menuTab19" class="menuTabButton"><a href="#">3. {l s='Api Logs' mod='ebay'}</a></li>
				</ul>


				<div id="tabList" class="{$class_general|escape:'htmlall':'UTF-8'} tab-content">
					<div id="menuTab1Sheet" class="tabItem tab-pane">
						{$form_parameters|ebayHtml}
					</div>

					<div id="menuTab101Sheet" class="tabItem tab-pane">
						{$form_parameters_annonces_tab|ebayHtml}
					</div>

					<div id="menuTab102Sheet" class="tabItem tab-pane">
						{$form_parameters_orders_tab|ebayHtml}
					</div>

					<div id="menuTab103Sheet" class="tabItem tab-pane">
						{$form_inter_shipping|ebayHtml}
					</div>

					<div id="menuTab13Sheet" class="tabItem tab-pane">
						{$form_advanced_parameters|ebayHtml}
					</div>

					<div id="menuTab3Sheet" class="tabItem tab-pane">
						{$form_shipping|ebayHtml}
					</div>

					<div id="menuTab4Sheet" class="tabItem tab-pane">
						{$form_template_manager|ebayHtml}
					</div>

					<div id="menuTab5Sheet" class="tabItem tab-pane">
						<div class="panel">
							{*$form_ebay_sync|ebayHtml*}

						</div>
					</div>

					<div id="menuTab9Sheet" class="tabItem tab-pane"><div class="panel">{$ebay_listings}</div></div>
					<div id="menuTab6Sheet" class="tabItem tab-pane"><div class="panel">{$table_orders}</div></div>
					<div id="menuTab14Sheet" class="tabItem tab-pane"><div class="panel">{$orders_sync}</div></div>
					<div id="menuTab79Sheet" class="tabItem tab-pane"><div class="panel">{$orders_returns_sync}</div></div>
					<div id="menuTab78Sheet" class="tabItem tab-pane"><div class="panel">{$order_returns}</div></div>
					{if isset($best_offers)}
						<div id="menuTab999Sheet" class="tabItem tab-pane">{$best_offers}</div>
					{/if}
					<div id="menuTab81Sheet" class="tabItem tab-pane">{$dashboard}</div>
					<div id="menuTab16Sheet" class="tabItem tab-pane"><div class="panel">{$orphan_listings}</div></div>
					<div id="menuTab80Sheet" class="tabItem tab-pane"><div class="panel">{$table_product_error}</div></div>
					<div id="menuTab106Sheet" class="tabItem tab-pane"><div class="panel">{$ebayProductsExcluTab}</div></div>
					<div id="menuTab17Sheet" class="tabItem tab-pane"><div class="panel">{$ebayLogJobs}</div></div>
					<div id="menuTab18Sheet" class="tabItem tab-pane"><div class="panel">{$ebayLogWorkers}</div></div>
					<div id="menuTab19Sheet" class="tabItem tab-pane"><div class="panel">{$ebayApiLogs}</div></div>
				</div>

				{*
				<script>
					{literal}
					$(".menuTabButton").click(function () {
						$(".menuTabButton.selected").removeClass("selected");
						$(this).addClass("selected");
						$(".tabItem.selected").removeClass("selected");
						$("#" + this.id + "Sheet").addClass("selected");
					});
					{/literal}
				</script>
				{if $id_tab}
					<script>
						$(".menuTabButton.selected").removeClass("selected");
						$("#menuTab{$id_tab|escape:'htmlall':'UTF-8'}").addClass("selected");
						$(".tabItem.selected").removeClass("selected");
						$("#menuTab{$id_tab|escape:'htmlall':'UTF-8'}Sheet").addClass("selected");
					</script>
				{/if}
				*}


				<div id="helpertexts" style="display:none;">
					<div id="returnsAccepted" style="width:300px">
						{l s='All sellers on eBay must specify a returns policy for their items, whether your policy is to accept returns or not. If you don\'t specify a returns policy, eBay will select a default returns policy for you.' mod='ebay'}
					</div>

					<div id="dispatchTime" style="width:300px">
						{l s='The dispatch time is the time between the buyer’s payment clearing and you sending the item. Buyers are increasingly expecting short dispatch times, ideally next day, but preferably within 3 working days. ' mod='ebay'}
					</div>

					<div id="DomShipp" style="width:300px">
						{l s='To add a shipping method, map your PrestaShop options with one offered by eBay.' mod='ebay'}
					</div>

					<div id="tagsTemplate" style="width:300px">
						{ldelim}MAIN_IMAGE{rdelim}<br/>
						{ldelim}MEDIUM_IMAGE_1{rdelim}<br/>
						{ldelim}MEDIUM_IMAGE_2{rdelim}<br/>
						{ldelim}MEDIUM_IMAGE_3{rdelim}<br/>
						{ldelim}PRODUCT_PRICE{rdelim}<br/>
						{ldelim}PRODUCT_PRICE_DISCOUNT{rdelim}<br/>
						{ldelim}DESCRIPTION_SHORT{rdelim}<br/>
						{ldelim}DESCRIPTION{rdelim}<br/>
						{ldelim}FEATURES{rdelim}<br/>
						{ldelim}EBAY_IDENTIFIER{rdelim}<br/>
						{ldelim}EBAY_SHOP{rdelim}<br/>
						{ldelim}SLOGAN{rdelim}<br/>
						{ldelim}PRODUCT_NAME{rdelim}
					</div>

					<div id="categoriesProgression" style="overflow: auto;width: 200px;height: 100px;text-align: center;font-size: 16px;padding-top: 30px;"></div>
				</div>
				<div class="clearfix"></div>
			</div>

		</div>
	</div>
	<div id="popin-container">
</div>
</div>
</div>

<script>
	{literal}
	var alert_exit_import_categories = "{/literal}{$alert_exit_import_categories}{literal}";
	function getKb(item) {
		item = typeof item !== 'undefined' ? item : 0;
		var that = $("a.kb-help:eq("+ item +")");

		$.ajax({
			type: "POST",
			url: formController,
			data: {
			    errorcode: $( that ).attr('data-errorcode'),
				lang: $( that ).attr('data-lang'),
				ajax: true,
				action: 'LoadKB'
			},
			dataType: "json",
			success: function(data)
			{
				if (data.result != 'error')
				{
					$( that ).addClass('active');
					$( that ).attr('href', data.result);
					$( that ).attr('target', '_blank');
				}
				var next = item + 1;
				if ($("a.kb-help:eq("+ next +")").length > 0)
					getKb(next);
			}
		});
	}
	jQuery(document).ready(function($) {
		getKb();
	});
	{/literal}
</script>
