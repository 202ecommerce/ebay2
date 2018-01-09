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

<script>
    var apilogs_ajax_link = '{$apilogs_ajax_link|addslashes}';
</script>
<style>
    #date_search_logs {
        line-height: inherit;
    }
</style>

<div id="contentLogApiTab" class="table-block">
    <h4 class="table-block__title table-block__holder">{l s='Api Logs' mod='ebay'}
       <button class="loadListApiLog button-refresh btn btn-default"><span class="icon-refresh"></span> {l s='Refresh' mod='ebay'}</button>
    </h4>
    <div class="table-block__search table-block__holder">
        <input type="text" class="form-control" id="id_prod_search_logs" value="{$search.id_product}"
               placeholder="{l s='by ID product' mod='ebay'}"
               title="{l s='by ID product' mod='ebay'}" data-toggle="tooltip">
        <input type="text" class="form-control" id="id_prod_atrr_search_logs" value="{$search.id_product_attribute}"
               placeholder="{l s='by ID product attribute' mod='ebay'}"
               title="{l s='by ID product attribute' mod='ebay'}" data-toggle="tooltip">
        <input type="text" class="form-control" id="status_search_logs" value="{$search.status}"
               placeholder="{l s='by Status' mod='ebay'}"
               title="{l s='by Status' mod='ebay'}" data-toggle="tooltip">
        <input type="text" class="form-control" id="type_search_logs"  value="{$search.type}"
               placeholder="{l s='by Type' mod='ebay'}"
               title="{l s='by Type' mod='ebay'}" data-toggle="tooltip">
        <input type="date" class="form-control" id="date_search_logs" value="{$search.date}"
               placeholder="{l s='by Date' mod='ebay'}"
               title="{l s='by Date' mod='ebay'}" data-toggle="tooltip">
        <button  id="searchBtnLogApi" class="button-apply btn btn-info"><span class="icon-search"></span> {l s='Apply' mod='ebay'}</button>
        <button class="button-reset btn btn-default" id="reset_apilogs"><span class="icon-close"></span> {l s='Reset' mod='ebay'}</button>

    </div>
    <div class="table-wrapper">
        <table class="table tableDnD" cellpadding="0" cellspacing="0" width="90%">
            <thead>
            <tr class="nodrag nodrop">
                <th style="width:30px";>
                    <label for="checkAll" title="Check all" data-toggle="tooltip">
                        <input id="checkAll" type="checkbox" class="apilogAllCheck">
                    </label>
                </th>
                <th>{l s='ID Product' mod='ebay'}</th>
                <th>{l s='ID Attribute' mod='ebay'}</th>
                <th>{l s='Status' mod='ebay'}</th>
                <th>{l s='Type' mod='ebay'}</th>
                <th>{l s='Date' mod='ebay'}</th>
                <th style="width:61px;"></th>
            </tr>
            </thead>
            <tbody>
            {if $logs}
                {foreach from=$logs item='log'}
                    <tr data-id_log="{$log.id_ebay_api_log|escape:'htmlall':'UTF-8'}">
                        <td><input id="checkbox" type="checkbox" class="apilogCheck" value="{$log.id_ebay_api_log|escape:'htmlall':'UTF-8'}"></td>
                        <td>{$log.id_product|escape:'htmlall':'UTF-8'}</td>
                        <td>{$log.id_product_attribute|escape:'htmlall':'UTF-8'}</td>
                        <td>{$log.status|escape:'htmlall':'UTF-8'}</td>
                        <td>{$log.type|escape:'htmlall':'UTF-8'}</td>
                        <td>{$log.date_add|escape:'htmlall':'UTF-8'}</td>
                        <td class="text-center">
                            <div class="action">
                                <a href="#" class="btn-hover-danger btn btn-sm btn-default view-api-log"
                                   title="{l s='View' mod='ebay'}" data-toggle="tooltip">
                                    <span class="icon-file-text"></span>
                                </a>
                                <a href="#" class="btn-hover-danger btn btn-sm btn-default delete-api-log"
                                   title="{l s='delete' mod='ebay'}" data-toggle="tooltip">
                                    <span class="icon-close"></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                {/foreach}
            {/if}
            <tr></tr>
            </tbody>
        </table>
    </div>

    {if $pages_all >1}
        <div class="navPaginationListLogApiTab" style="display:flex; justify-content:center">
            {include file=$tpl_include}
        </div>
    {/if}

    <div class="buttonContainer table-block__holder table-block__footer-buttons">
        <button class="btn btn-danger deleteAllApiLog">{l s='Delete all' mod='ebay'}</button>
        <button class="btn btn-danger deleteSelectedApiLog">{l s='Delete selected' mod='ebay'}</button>
    </div>
</div>

<div id="popin-cofirm-delete-api-log" class="popin popin-sm" style="display: none;position: fixed;z-index: 1;left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgb(0,0,0);background-color: rgba(0,0,0,0.4);">
    <div class="panel" style=" background-color: #fefefe;padding: 20px;border: 1px solid #888;width: 80%;margin: 30% auto;">
        <div class="panel-heading">
            <i class="icon-trash"></i> {l s='Synchronization product' mod='ebay'}
        </div>
        <p>{l s='Do you want delete Api Logs?' mod='ebay'}</p>

        <div class="panel-footer" style="display: flex; justify-content: space-between; align-items: center">
            <button class="cancel-delete-api-log btn btn-default"><i class="process-icon-cancel"></i>{l s='Annuler' mod='ebay'}</button>
            <button class="ok-delete-api-log btn btn-success pull-right" style="padding:15px">{l s='OK' mod='ebay'}</button>
        </div>
    </div>
</div>

<div id="popin-api-log-dateils" class="popin popin-sm" style="display: none;position: fixed;z-index: 2;left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgb(0,0,0);background-color: rgba(0,0,0,0.4);">
    <div class="panel" style=" background-color: #fefefe;padding: 20px;border: 1px solid #888;width: 80%;margin: 30% auto;">
        <div class="panel-heading">
            <i class="icon-trash"></i> {l s='Log Details' mod='ebay'}
        </div>
        <div style="overflow: auto;">
            <span class="h4">{l s='Date :' mod='ebay'}</span>
            <span id="api_request_date"></span></br>
            <span class="h4">{l s='Status :' mod='ebay'}</span>
            <span id="api_request_status"></span></br>
            <span class="h4">{l s='Request :' mod='ebay'}</span></br>
            <code id="api_request"></code></br>
            <span class="h4">{l s='Response :' mod='ebay'}</span></br>
            <code id="api_response"></code>
        </div>
        <div class="panel-footer" style="display: flex; justify-content: space-between; align-items: center">
            <button class="close-api-log-details btn btn-default"><i class="process-icon-cancel"></i>{l s='Annuler' mod='ebay'}</button>
        </div>
    </div>
</div>

{literal}
<script>
    var row_for_delete;
    $(document).on('click', '.delete-api-log', function(){
        $('#popin-cofirm-delete-api-log').show();
        row_for_delete = $(this).closest('tr');
    });

    $(document).on('click', '.cancel-delete-api-log', function(){
        $('#popin-cofirm-delete-api-log').hide();
    });
    $(document).on('click', '.close-api-log-details', function(){
        $('#popin-api-log-dateils').hide();
    });

    $(document).on('click', '.ok-delete-api-log', function(){
        $('#popin-cofirm-delete-api-log').hide();
        var id_log = [$(row_for_delete).attr('data-id_log')];
        deleteLogsByIds(id_log);
    });

    $(document).on('click', '.loadListApiLog', function(){
        $.ajax({
            url: apilogs_ajax_link+'&configure=ebay',
            data: {
                ajax: true,
                action: 'PaginationLogApi',
                id_employee: id_employee,
                profile: id_ebay_profile,
                page: 1
            },
            beforeSend : function(){
                $('#contentLogApiTab').empty();
                var html = '<div class="ajaxLoadingFormSyncTab" style="position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px;"></div>';
                $('#contentLogApiTab').append(html);
            },
            success: function(data)
            {
                $('#contentLogApiTab').parent().html(data);
            }
        });
    });

    $(document).on('click', '.navPaginationListLogApiTab .pagination span', function(){
        var page = $(this).attr('value');
        if(page){
            $.ajax({
                url: apilogs_ajax_link+'&configure=ebay',
                data: {
                    ajax: true,
                    action: 'PaginationLogApi',
                    id_employee: id_employee,
                    profile: id_ebay_profile,
                    page: page
                },
                beforeSend : function(){
                    $('#contentLogApiTab').empty();
                    var html = '<div class="ajaxLoadingFormSyncTab" style="position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px;"></div>';
                    $('#contentLogApiTab').append(html);
                },
                success: function(data)
                {
                    $('#contentLogApiTab').parent().html(data);
                }
            });
        }
    });

    $(document).on('click', '.apilogAllCheck', function(){
        var state = $(this).prop('checked');
        $('.apilogCheck').prop('checked', state);
    });

    $(document).on('click', '.deleteAllApiLog', function(){
        deleteLogsByIds('all');
    });

    $(document).on('click', '.deleteSelectedApiLog', function(){
        var ids = [];
        $('.apilogCheck:checked').each(function(){
            ids.push($(this).attr('value'));
        });
        deleteLogsByIds(ids);
    });

    function deleteLogsByIds(ids_logs) {
        if (ids_logs == 'all' || ids_logs.length != 0){
            $.ajax({
                url: apilogs_ajax_link+'&configure=ebay',
                data: {
                    ajax: true,
                    action: 'deleteApiLogs',
                    ids_logs : ids_logs,
                    type : 'log',
                },
                success : function(data){
                    if (ids_logs == 'all'){
                        $('#contentLogApiTab').empty();
                    } else{
                        $('.apilogCheck').each(function(){
                            if (ids_logs.indexOf($(this).attr('value')) != -1){
                                $(this).closest('tr').remove();
                            }
                        });
                    }

                }
            })
        }

    }

    $(document).on('click', '.view-api-log', function(){
        var id = $(this).closest('tr').attr('data-id_log');
        $.ajax({
            url: apilogs_ajax_link+'&configure=ebay',
            data: {
                ajax: true,
                action: 'getApiLogDetails',
                id_log: id,
            },
            success : function(data){
                var data = jQuery.parseJSON(data);

                $('#api_request_date').html(data.date_add);
                $('#api_request_status').html(data.status);
                $('#api_request').text(data.request);
                $('#api_response').text(data.response);
                $('#popin-api-log-dateils').show();
            }
        })
    });

    $('#searchBtnLogApi').click(search);
    $('#reset_apilogs').click(function(){
            $.ajax({
                url: apilogs_ajax_link+'&configure=ebay',
                data: {
                    ajax: true,
                    action: 'PaginationLogApi',
                    id_employee: id_employee,
                    profile: id_ebay_profile,
                    page: 1
                },
            beforeSend : function(){
                $('#contentLogApiTab').empty();
                var html = '<div class="ajaxLoadingFormSyncTab" style="position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px;"></div>';
                $('#contentLogApiTab').append(html);
            },
            success: function(data)
            {
                $('#contentLogApiTab').parent().html(data);
            }
        });
    }
    );
    function search() {
        var id_prod;
        var id_prod_attr;
        var status;
        var type;
        var date;

        id_prod = $('#id_prod_search_logs').attr('value');
        id_prod_attr = $('#id_prod_atrr_search_logs').attr('value');
        status = $('#status_search_logs').attr('value');
        type = $('#type_search_logs').attr('value');
        date = $('#date_search_logs').attr('value');

        $.ajax({
            url: apilogs_ajax_link+'&configure=ebay',
            data: {
                ajax: true,
                action: 'PaginationLogApi',
                id_shop: id_shop,
                page: 1,
                id_prod: id_prod,
                id_prod_attr: id_prod_attr,
                status: status,
                type: type,
                date: date,
                profile: id_ebay_profile,
            },
            beforeSend : function(){
                $('#contentLogApiTab').empty();
                var html = '<div class="ajaxLoadingFormSyncTab" style="position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px;"></div>';
                $('#contentLogApiTab').append(html);
            },
            success: function(data)
            {
                $('#contentLogApiTab').parent().html(data);
            }
        });
    }

</script>
{/literal}



{* Bootstrap tooltip *}
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();

    });
</script>
