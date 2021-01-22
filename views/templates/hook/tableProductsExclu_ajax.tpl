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

<div id="ebayProductExclu">
    <div class="text-center">
        <button class="btn btn-default">
            <i class="icon-eye"></i>
            <span>{l s='See excluded products' mod='ebay'}</span>
        </button>
    </div>
</div>


<script type="text/javascript">
    var ebayProductExcluController = "{$ebayProductExcluController|addslashes}";
    var url_tab = "{$url_tab}";
    function loadProductExclu(){
    $.ajax({
        type: "POST",
        url: ebayProductExcluController,
        data: "ajax=true&action=GetProductExclu&id_employee={$id_employee|escape:'htmlall':'UTF-8'}",
        success: function(data)
        {
            $('#ebayProductExclu').fadeOut(400, function(){
                $(this).html(data).fadeIn();
            })
        }
    });
    }
    var content_ebay_listings = $("#ebayProductExclu button");
    content_ebay_listings.bind('click', 'button', function(){
        loadProductExclu();
    });
    //]]>
</script>