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



function delete_orphan(index, element) {


    var lnk = $(element);

    var id_product_ref = $(element).attr('ref');

    $.ajax({
        type: "POST",
        url: module_dir + "ebay/ajax/deleteOrphanListing.php?token=" + ebay_token + "&id_lang=" + id_lang + "&id_product_ref=" + id_product_ref + "&id_employee=" + id_employee + "&action=end",
        success : function(data) {

            if (data == '1')
                lnk.closest('tr').remove(); // remove row

        }
    });


}

function delete_orphan_by_checkbox(index, element){

    var a = $(element).closest('tr').find('a.delete-orphan');
    var lnk = $(a);

    var id_product_ref = $(a).attr('ref');

    $.ajax({
        type: "POST",
        url: module_dir + "ebay/ajax/deleteOrphanListing.php?token=" + ebay_token + "&id_lang=" + id_lang + "&id_product_ref=" + id_product_ref + "&id_employee=" + id_employee + "&action=end",
        success : function(data) {

            if (data == '1')
                lnk.closest('tr').remove(); // remove row

        }
    });
}

function delete_all_orphans() {
    $('a.delete-orphan').each(delete_orphan);
}

function delete_several_orphans() {
    $('input.checkboxfordelete:checked').each(delete_orphan_by_checkbox);
}

var element_for_delete;

$(document).ready(function () {

    $(document).on('click', '.delete_all_orphans', function(){
        $('#popin-delete-all-products').show();
    });

    $(document).on('click', '#popin-delete-all-products .cancel-delete', function(){
        $('#popin-delete-all-products').hide();
    });

    $(document).on('click', '#popin-delete-all-products .ok-delete', function(){
        delete_all_orphans();
        $('#popin-delete-all-products').hide();
    });

    $('.delete-orphan').live('click', 'a', function(e){
        e.preventDefault();
        $('#popin-delete-product').show();
        element_for_delete = $(this);
    });

    $(document).on('click', '#popin-delete-product .cancel-delete', function(){
        $('#popin-delete-product').hide();
    });

    $(document).on('click', '#popin-delete-product .ok-delete', function(){
        $('#popin-delete-product').hide();
        delete_orphan(0, element_for_delete);
    });

    $(document).on('click', '.delete_several_orphans', function(){
        $('#popin-delete-several-products').show();
    });

    $(document).on('click', '#popin-delete-several-products .cancel-delete', function(){
        $('#popin-delete-several-products').hide();
    });

    $(document).on('click', '#popin-delete-several-products .ok-delete', function(){
        delete_several_orphans();
        $('#popin-delete-several-products').hide();
    });

    /*$('.delete-orphan').live('click', 'a', function(e) {

        e.preventDefault();

        if (!confirm(orphan_listings_ebay_l['Remove this ad?']))
            return;

        var lnk = $(this);

        var id_product_ref = $(this).attr('ref');

        $.ajax({
            type: "POST",
            url: module_dir + "ebay/ajax/deleteOrphanListing.php?token=" + ebay_token + "&id_lang=" + id_lang + "&id_product_ref=" + id_product_ref + "&id_employee=" + id_employee + "&action=end",
            success : function(data) {

                if (data == '1')
                    lnk.parent().parent().remove(); // remove row

            }
        });


    });

    $('.out_of_stock_orphan').live('click', 'a', function(e) {
        var id_product_ref = $(this).attr('ref');
        $.ajax({
            type: 'POST',
            url: module_dir + 'ebay/ajax/deleteOrphanListing.php',
            data: "token=" + ebay_token + "&id_ebay_profile="+ id_ebay_profile +"&id_product_ref=" + id_product_ref +"&action=out_of_stock" + "&id_employee=" + id_employee,
            success: function (data) {
                $(this).parent().parent().remove();
            }
        });

    });*/

    var content_ebay_relistings = $("#ebayOrphanReListing button.loadListOrphan");
    content_ebay_relistings.live('click', 'button', function () {
        loadOrphans();
    });

    $(document).on('click', '.navPaginationListOrphanProductTab .pagination span', function(){
        var page = $(this).attr('value');
        if(page){
            $.ajax({
                type: "POST",
                url: module_dir+'ebay/ajax/loadTableOrphanListings.php',
                data: "token="+ebay_token+"&id_employee="+ id_employee +"&profile=" + id_ebay_profile + "&page=" + page,
                beforeSend : function(){
                    $('#ebayOrphanListing').empty();
                    var html = '<div class="ajaxLoadingFormSyncTab" style="position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px;"></div>';
                    $('#ebayOrphanListing').append(html);
                },
                success: function(data)
                {
                    $('#ebayOrphanListing').fadeOut(400, function(){
                        $(this).html(data).fadeIn();
                    })
                }
            });
        }


    });

});
/*
$(document).ready(function() {
});
*/

