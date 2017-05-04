/*
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
*	@author    PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2017 PrestaShop SA
*	@license   http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*/

function loadOrphans() {

    $.ajax({
        type: "POST",
        url: module_dir+'ebay/ajax/loadTableOrphanListings.php',
        data: "token="+ebay_token+"&id_employee="+ id_employee +"&profile=" + id_ebay_profile,
        success: function(data)
        {
            $('#ebayOrphanListing').fadeOut(400, function(){
                $(this).html(data).fadeIn();
            })
        }
    });

  
}
$(document).ready(function () {
$('.delete-orphan').live('click', 'a', function(e) {

    e.preventDefault();

    if (!confirm(orphan_listings_ebay_l['Remove this ad?']))
        return;

    var lnk = $(this);

    var id_product_ref = $(this).attr('ref');

    $.ajax({
        type: "POST",
        url: module_dir + "ebay/ajax/deleteOrphanListing.php?token=" + ebay_token + "&id_lang=" + id_lang + "&id_product_ref=" + id_product_ref + "&id_employee=" + id_employee,
        success : function(data) {

            if (data == '1')
                lnk.parent().parent().remove(); // remove row

        }
    });


})
    var content_ebay_relistings = $("#ebayOrphanReListing button");
    content_ebay_relistings.live('click', 'button', function(){
        loadOrphans();
    });
});
/*
$(document).ready(function() {
});
*/

