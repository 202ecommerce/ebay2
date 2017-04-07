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

    $('.panel-heading').html($('.list-group-item.active.main-menu-a').html());

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



    $('.js-popin').fancybox({
        'modal': true,
        'showCloseButton': false,
        'padding': 0,
        'parent': '#popin-container',
    });

    // Close modal on click on cancel button
    $(document).on('click', '.js-close-popin', function() {
        $.fancybox.close();
    });

    function selectMainTab(menu_name) {

        $('.main-menu-a').removeClass('active');
        $('#' + menu_name + '-menu-link').addClass('active');
        $('.panel-heading').html($('#' + menu_name + '-menu-link').html());
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
