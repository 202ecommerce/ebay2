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
*	@copyright	2007-2018 PrestaShop SA
*	@license   http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*/


$(document).ready(function(){


    $.ajax({
        type: "POST",
        url: formEbaySyncController, //formEbaySyncController was defined in form.tpl
        data: {
            ajax: true,
            action: 'LoadFormSyncTab'
        },
        beforeSend:function(){
            var html = '<div class="ajaxLoadingFormSyncTab" style=" position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px;"></div>';
            $('#menuTab5Sheet .panel').empty();
            $('#menuTab5Sheet .panel').append(html);
        },
        success: function(data)
        {
            $('#menuTab5Sheet .panel').append(data);
            $('.ajaxLoadingFormSyncTab').hide();

        }
    });

    $(document).on('click', '.navPaginationSyncTab .pagination span', function(){
        var page = $(this).attr('value');
        var searche = $('#searcheEbaySync .name_cat').attr('value');
        var id_prod = $('#searcheEbaySync .id_prod').attr('value');
        var name_prod = $('#searcheEbaySync .name_prod').attr('value');
        if(page){
            $.ajax({
                type: "POST",
                url: formEbaySyncController, //formEbaySyncController was defined in form.tpl
                data: {
                    ajax: true,
                    action: 'LoadFormSyncTab',
                    page: page,
                    searche: searche,
                    id_product: id_prod,
                    name_product: name_prod,
                },
                beforeSend:function(){
                    var html = '<div class="ajaxLoadingFormSyncTab" style="position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px;"></div>';
                    $('#menuTab5Sheet .panel').empty();
                    $('#menuTab5Sheet .panel').append(html);
                },
                success: function(data)
                {
                    $('#menuTab5Sheet .panel').append(data);
                    $('.ajaxLoadingFormSyncTab').hide();

                }
            });
        }
    });

    $(document).on('click', '#searcheEbaySync .searcheBtn', function(){
        var searche = $('#searcheEbaySync .name_cat').attr('value');
        var id_prod = $('#searcheEbaySync .id_prod').attr('value');
        var name_prod = $('#searcheEbaySync .name_prod').attr('value');
        if(searche || id_prod || name_prod){
            $.ajax({
                type: "POST",
                url: formEbaySyncController, //formEbaySyncController was defined in form.tpl
                data: {
                    ajax: true,
                    action: 'LoadFormSyncTab',
                    searche: searche,
                    id_product: id_prod,
                    name_product: name_prod,
                },
                beforeSend:function(){
                    var html = '<div class="ajaxLoadingFormSyncTab" style="position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px;"></div>';
                    $('#menuTab5Sheet .panel').empty();
                    $('#menuTab5Sheet .panel').append(html);
                },
                success: function(data)
                {
                    $('#menuTab5Sheet .panel').append(data);
                    $('.ajaxLoadingFormSyncTab').hide();

                }
            });
        }
    });

    $(document).on('click', '#searcheEbaySync .researcheBtn', function(){

            $.ajax({
                type: "POST",
                url: formEbaySyncController, //formEbaySyncController was defined in form.tpl
                data: {
                    ajax: true,
                    action: 'LoadFormSyncTab',
                },
                beforeSend:function(){
                    var html = '<div class="ajaxLoadingFormSyncTab" style="position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px;"></div>';
                    $('#menuTab5Sheet .panel').empty();
                    $('#menuTab5Sheet .panel').append(html);
                },
                success: function(data)
                {
                    $('#menuTab5Sheet .panel').append(data);
                    $('.ajaxLoadingFormSyncTab').hide();

                }
            });

    });

    $(document).on('click', '.button-sync .dropdown-menu li', function(event){
        var modeResync = $(this).attr('data-action');
        $('#popin-cofirm-resync input[data-role="modeResync"]').attr('value', modeResync);
        $('#popin-cofirm-resync').show();
    });

    $(document).on('click', '.cancel-resyncProducts', function(){
        $('#popin-cofirm-resync').hide();
    });

    $(document).on('click', '.ok-resyncProducts', function(){
        $('#popin-cofirm-resync').hide();
        resyncProducts();
    });

    function resyncProducts(){
        var modeResync = $('#popin-cofirm-resync input[data-role="modeResync"]').attr('value');
        $.ajax({
            type: "POST",
            url: formEbaySyncController, //formEbaySyncController was defined in form.tpl
            data: {
                ajax: true,
                action: 'ButtonResyncProducts',
                id_ebay_profile: id_ebay_profile,
                modeResync: modeResync,
            },

        });
        location.reload();
    }
});


  






