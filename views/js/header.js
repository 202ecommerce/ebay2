/*
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
 * International Registered Trademark & Property of PrestaShop SA
 */

var profileId ;
var boost;
$(document).ready(function () {
    $('#ebay-seller-tips-link').click(function (event) {
        event.preventDefault();
        var sellerTips = $('#seller-tips');
        if (sellerTips.css('display') == 'none') {
            $(this).html(header_ebay_l['Hide seller tips']);
            sellerTips.show();
        } else {
            $(this).html(header_ebay_l['Show seller tips']);
            sellerTips.hide();
        }
        return false;
    });

    $('#tab_title_level1').html($('.list-group-item.active.main-menu-a').html());

    $('.delete-profile').click(function (event) {
        event.preventDefault();
        profileId = $(this).data('profile');
        $('#popin-delete-profile').show();
        $("#popin-delete-profile .id_profile").html(profileId);
    });

    $(document).on('click', '#popin-delete-profile .cancel-delete', function(){
        $('#popin-delete-profile').hide();
    });


    $(document).on('click', '#popin-delete-profile .ok-delete', function(){
        $('#popin-delete-profile').hide();
        $.ajax({
            type: "POST",
            url: formController,
            data: {
                ajax: true,
                action: 'DeleteProfile',
                profile: profileId,
            },
            cache: false,
            success: function (data) {
                window.location.replace(window.location.href.replace("&action=logged", ""));
            }
        });
    });

    $('.star_boost').click(function (event) {
        event.preventDefault();
        $('.js-close-popin_boost').show();
        $('.close_boost').hide();
        boost = window.setInterval(function(){startBoost(cron_url)}, 3000);
    });




    // Close modal on click on cancel button
    $(document).on('click', '.js-close-popin', function() {
        $.fancybox.close();
        $('div.first_page_popin select').each(function () {
            $(this).removeClass('form-error');
        });
        $('div.first_page_popin .text-error').each(function () {
            $(this).hide();
        });
        $('.js-next-popin').show();
        $('.category_ps_list').html('');
        $('#form_product_to_sync').html('');
        $('#form_variations_to_sync').html('');
        $('#impact_prix').val('');
        var courant_page = $('.page_config_category.selected');
        courant_page.removeClass('selected').hide();
        var courant_page = $('.page_boost.selected');
        courant_page.removeClass('selected').hide();
        $('.page_popin').html('1');
        $('.page_popin_boost').html('1');
        $('.first_page_popin').addClass('selected').show();
        $('.js-prev-popin').hide();
        $('.js-prev-popin_boost').hide();
        $('.js-next-popin_boost').show();
        $('#ps_category_list').show();
        $('#item_spec').html('');
        $('div .category_ebay').html('');
        $('#ps_category_autocomplete_input').val('');
        $('#item_spec').children().remove();
        $('select[name="payement_policies"]').find('option:selected').removeAttr("selected");
        $('select[name="return_policies"]').find('option:selected').removeAttr("selected");
        $('select[name="store_category"]').find('option:selected').removeAttr("selected");
        $('.product_sync_info').show();
    });

    $(document).on('click', '.js-close-popin_boost', function() {
        $.fancybox.close();
        var courant_page = $('.page_config_category.selected');
        courant_page.removeClass('selected').hide();
        var courant_page = $('.page_boost.selected');
        courant_page.removeClass('selected').hide();
        $('.page_popin_boost').html('1');
        $('.first_page_popin').addClass('selected').show();
        $('.js-prev-popin_boost').hide();
        $('.js-next-popin_boost').show();
        $('.percentages').html('0%');
        $('.valueMax').html('Loading...');
        $('.percentages_line').css('width', '0%');
        $('.valueDone').html('0');
        $('.valueMax').removeData('val');
        clearInterval(boost);
        $('.js-close-popin_boost').hide();
        $('.close_boost').show();
    });

    function selectMainTab(menu_name) {

        $('.main-menu-a').removeClass('active');
        $('#' + menu_name + '-menu-link').addClass('active');
        $('#tab_title_level1').html($('#' + menu_name + '-menu-link').html());
        $('.menuTab').hide();
        $('.menu-msg').hide();
        var menu = $('.' + menu_name + '-menu');
        $('.' + menu_name + '-menu').show();

        menu.children(":first").trigger('click');

    }

    function startBoost(url) {
        $.ajax({
            type: 'POST',
            url: formController,
            data: {
                ajax: true,
                action: 'BoostMode',
                cron_url: url,
            },
            success: function (data) {
               if (typeof $('.valueMax').data('val') === 'undefined'){
                   $('.valueMax').data('val', data);
                   $('.valueMax').html(data);
               }
               var complited = $('.valueMax').data('val') - data;
                
                $('.valueDone').html(complited);
                var complitedPercent = 0;
                if(complited > 0) {
                     complitedPercent = Math.round(complited/($('.valueMax').data('val')/100));

                }
                if(complited == $('.valueMax').data('val')) {
                    complitedPercent = 100;
                    $('.percentages').html(complitedPercent+'%');
                    $('.percentages_line').css('width', complitedPercent+'%');
                    //event.preventDefault();
                    clearInterval(boost);
                    var urlReload = location.href.split('?')[0] + "?";
                    var search = 'configure=ebay&controller=AdminModules&'
                    var searchOldArray = location.search.split('&');
                    searchOldArray.forEach(function(element){
                        if (element.indexOf('token') != -1) {
                            search += element;
                        }
                    });
                    location = urlReload + search;
                } else{
                    $('.percentages').html(complitedPercent+'%');
                    $('.percentages_line').css('width', complitedPercent+'%');
                }
            }
        });

    }

    $('.main-menu-a').click(function (event) {

        event.preventDefault();

        var menuName = $(this).data('sub');

        selectMainTab(menuName);

        return false;
    });

    $(".menuTabButton").click(function () {

        $(".menuTabButton.active").removeClass("active");
        $(this).addClass("active");
        $(".tabItem.active").removeClass("active");
        $("#" + this.id + "Sheet").addClass("active");

        if ($(this).attr('id') == 'menuTab15') {
            loadPrestaShopProducts(1);
        } else if ($(this).attr('id') == 'menuTab16') {

        }

    });

    selectMainTab(main_tab);

    if (id_tab) {
        $(".menuTabButton.active").removeClass("active");
        $("#menuTab" + id_tab).addClass("active");
        $(".tabItem.active").removeClass("active");
        $("#menuTab" + id_tab + "Sheet").addClass("active");
    }


    // Stop default behavior for links <a href="#">
    $(document).on('click', 'a[href="#"]', function (event) {
        event.preventDefault();
    });
});
