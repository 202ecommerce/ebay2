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

<!-- ebay -->
<div class="error" onClick="$('#menuTab1').click();" style="cursor:pointer;">
	<b>{l s='You have to configure "Account settings" tab before using this tab.' mod='ebay'}</b>
</div>
{if (isset($error_form_category) && $error_form_category)}
	<script type="text/javascript">
		$(document).ready(function(){
			$("#menuTab2").addClass('wrong');
		})
	</script>
{/if}
{if (isset($error_form_shipping) && $error_form_shipping)}
	<script type="text/javascript">
		$(document).ready(function(){
			$("#menuTab3").addClass('wrong');
		})
	</script>
{/if}
<script type="text/javascript">
	// <![CDATA[
	$(document).ready(function(){
		var form_categories = parseInt("0");
		if (form_categories >= 1)
			$("#menuTab2").addClass('success');
		
		else
			$("#menuTab2").addClass('wrong');
	});
	//]]>
</script>
<!-- /ebay -->
