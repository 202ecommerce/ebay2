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

<div id="ebayListings">
	<div class="text-center">
		<button class="btn btn-default">
			<i class="icon-eye"></i>
			<span>{l s='See eBay listings' mod='ebay'}</span>
		</button>
	</div>
</div>
<script type="text/javascript">
	// <![CDATA[
    var ebayListingsController = "{$ebayListingsController|addslashes}";
	var content_ebay_listings = $("#ebayListings button");
	content_ebay_listings.bind('click', 'button', function(){
		$.ajax({
			type: "POST",
			url: ebayListingsController,
			data: "ajax=true&action=GetEbayListings&id_employee={$id_employee|escape:'htmlall':'UTF-8'}&id_shop="+id_shop+"&admin_path={$admin_path|escape:'htmlall':'UTF-8'}&page=1",
			success: function(data)
			{
				$('#ebayListings').fadeOut(400, function(){
					$(this).html(data).fadeIn();
				})
			}
		});
	});

    $(document).on('click', '.navPaginationListProductTab .pagination span', function(){
        var page = $(this).attr('value');
        if(page){
            var id_prod;
            var id_prod_ebay;
            var name_cat;
            var name_prod;

            id_prod = $('#id_prod_search').val();
            id_prod_ebay = $('#id_prod_ebay_search').val();
            name_cat = $('#name_cat_search').val();
            name_prod = $('#name_prod_search').val();

            var data = {
                id_employee: {$id_employee|escape:'htmlall':'UTF-8'},
                id_shop: id_shop,
                admin_path: "{$admin_path|escape:'htmlall':'UTF-8'}",
                page: page,
                id_prod: id_prod,
                id_prod_ebay: id_prod_ebay,
                name_cat: name_cat,
                name_prod: name_prod,
                ajax: true,
                action: 'GetEbayListings'
			};

            $.ajax({
                type: "POST",
                url: ebayListingsController,
                data: data,
                success: function(data)
                {
                    $('#ebayListings').fadeOut(400, function(){
                        $(this).html(data).fadeIn();
                    })
                   
                }
            });
        }


    });
	//]]>
</script>
