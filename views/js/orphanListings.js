/*
 *  2007-2022 PrestaShop
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 202-ecommerce <tech@202-ecommerce.com>
 *  @copyright Copyright (c) 2007-2022 202-ecommerce
 *  @license Commercial license
 *  International Registered Trademark & Property of PrestaShop SA
 *
 */

function loadOrphans() {

    $.ajax({
        type: "POST",
        url: ebayOrphanListingsController,
        data: {
            id_employee: id_employee,
            profile: id_ebay_profile,
            ajax: true,
            action: 'LoadTableOrphanListings',
        },
        success: function (data) {
            $('#ebayOrphanListing').fadeOut(400, function () {
                $(this).html(data).fadeIn();
            })
        }
    });

  
}

function searchOrphan() {
    var id_prod_ebay;
    var name_product;
    var ps_category;
    var ebay_category;

    id_prod_ebay = $('#id_product_ebay_search').attr('value');
    name_product = $('#name_product_search').attr('value');
    ps_category = $('#ps_category_search').attr('value');
    ebay_category = $('#ebay_category_search').attr('value');

    var data = {
        id_employee: id_employee,
        profile: id_ebay_profile,
        id_shop: id_shop,
        page: 1,
        name_product: name_product,
        id_prod_ebay: id_prod_ebay,
        ps_category: ps_category,
        ebay_category: ebay_category,
        ajax: true,
        action: 'LoadTableOrphanListings',
    };

    $.ajax({
        type: "POST",
        url: ebayOrphanListingsController,
        data: data,
        success: function (data) {
            $('#ebayOrphanListing').fadeOut(400, function () {
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
        url: ebayOrphanListingsController,
        data: {
            id_lang: id_lang,
            id_product_ref: id_product_ref,
            id_employee: id_employee,
            listing_action: 'end',
            ajax: true,
            action: 'DeleteOrphanListing',
        },
        success : function(data) {
            console.log(data);
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
        url: ebayOrphanListingsController,
        data: {
            id_lang: id_lang,
            id_product_ref: id_product_ref,
            id_employee: id_employee,
            listing_action: 'end',
            ajax: true,
            action: 'DeleteOrphanListing',
        },
        success : function(data) {
            console.log(data);
            if (data == '1')
                lnk.closest('tr').remove(); // remove row

        }
    });
}

function delete_all_orphans() {
    $.ajax({
        type: "POST",
        url: ebayOrphanListingsController,
        data: {
            id_lang: id_lang,
            id_employee: id_employee,
            id_ebay_profile: id_ebay_profile,
            ajax: true,
            action: 'DeleteAllOrphanListing',
        },
        beforeSend: function() {
            $('.delete_all_orphans').prop('disabled', true);
            $('.delete_several_orphans').prop('disabled', true);
        },
        success : function(data) {
            console.log(data);
            if (data == '1') {
                $('#OrphanListings').remove();
                $('.navPaginationListOrphanProductTab').remove();
                $('.delete_all_orphans').prop('disabled', false);
                $('.delete_several_orphans').prop('disabled', false);
            }


        }
    });
}

function delete_several_orphans() {
    $('input.checkboxfordelete:checked').each(delete_orphan_by_checkbox);
}

var element_for_delete;

$(document).ready(function () {

    $(document).on('click', '.orphanCheckAll', function(){
        var state = $(this).prop('checked');
        $('.checkboxfordelete').prop('checked', state);
    });

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


    var content_ebay_relistings = $("#ebayOrphanReListing button.loadListOrphan");
    content_ebay_relistings.live('click', 'button', function () {
        loadOrphans();
    });
    var resetBtn = $('#reset_list_orphanEbay');
    resetBtn.live('click', 'button', function () {
        loadOrphans();
    });
    var applyBtn = $('#searchBtnOrphan');
    applyBtn.live('click', 'button', function () {
        searchOrphan();
    });

});
/*
$(document).ready(function() {
});
*/

