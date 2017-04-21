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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright  2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

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
        var profileId = $(this).data('profile');
        if (confirm(header_ebay_l['Are you sure you want to delete the profile number %profile_number%?'].replace('%profile_number%', profileId))) {
            $.ajax({
                type: "POST",
                url: delete_profile_url + '&profile=' + profileId,
                cache: false,
                success: function (data) {
                    window.location.replace(window.location.href.replace("&action=logged", ""));
                }
            });
        }
        return false;
    });


    $('.js-popin').live('click', function() {

        $('.js-popin').fancybox({
            'modal': true,
            'showCloseButton': false,
            'padding': 0,
            'parent': '#popin-container',
        });
        $('.product_sync_info').hide();
        if($('ul.category_ps_list li').length == 0) {
            $('.js-next-popin').attr('disabled','disabled');
        }
    });

    // Close modal on click on cancel button
    $(document).on('click', '.js-close-popin', function() {
        $.fancybox.close();

        $('.js-next-popin').show();
        $('.category_ps_list').html('');
        $('#form_product_to_sync').html('');
        $('#form_variations_to_sync').html('');
        $('#impact_prix').val('');
        var courant_page = $('.page_config_category.selected');
        courant_page.removeClass('selected').hide();
        $('.page_popin').html('1');
        $('.first_page_popin').addClass('selected').show();
        $('.js-prev-popin').hide();
        $('#ps_category_list').show();
        $('#item_spec').html('');
        $('#item_spec').children().remove();
        $('select[name="payement_policies"]').find('option:selected').removeAttr("selected");
        $('select[name="return_policies"]').find('option:selected').removeAttr("selected");
        $('select[name="store_category"]').find('option:selected').removeAttr("selected");
        $('.product_sync_info').show();
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
            loadOrphans();
        }

    });

    selectMainTab(main_tab);

    if (id_tab) {
        $(".menuTabButton.active").removeClass("active");
        $("#menuTab" + id_tab).addClass("active");
        $(".tabItem.active").removeClass("active");
        $("#menuTab" + id_tab + "Sheet").addClass("active");
    }



});
