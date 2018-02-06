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
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script>
    var apilogs_ajax_link = '{$apilogs_ajax_link|addslashes}';
</script>

<div id="contentLogWorkersTab" class="table-block">
    <h4 class="table-block__title table-block__holder">{l s='Workers' mod='ebay'}
        <button class="loadListWorker button-refresh btn btn-default"><span class="icon-refresh"></span> {l s='Refresh' mod='ebay'}</button>
    </h4>

    <div class="table-wrapper">
        <table class="table tableDnD" cellpadding="0" cellspacing="0" width="90%">
            <thead>
            <tr class="nodrag nodrop">
                <th style="width:30px;">
                    <label for="checkAll" title="Check all" data-toggle="tooltip">
                        <input id="checkAll" type="checkbox" class="workAllCheck">
                    </label>
                </th>
                <th>{l s='ID' mod='ebay'}</th>
                <th>{l s='Date' mod='ebay'}</th>
                <th>{l s='ID Product' mod='ebay'}</th>
                <th>{l s='ID Attribute' mod='ebay'}</th>
                <th style="width:61px;"></th>
            </tr>
            </thead>
            <tbody>
            {if $tasks}
                {foreach from=$tasks item='task'}
                    <tr>
                        <td><input id="checkbox" type="checkbox" class="workCheck" value="{$task.id|escape:'htmlall':'UTF-8'}"></td>
                        <td>{$task.id|escape:'htmlall':'UTF-8'}</td>
                        <td>{$task.date_add|escape:'htmlall':'UTF-8'}</td>
                        <td>{$task.id_product|escape:'htmlall':'UTF-8'}</td>
                        <td>{$task.id_product_attribute|escape:'htmlall':'UTF-8'}</td>
                        <td class="text-center">
                            <div class="action">
                                <a href="#" class="btn-hover-danger btn btn-sm btn-default">
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
        <div class="navPaginationListLogWorkersTab" style="display:flex; justify-content:center">
            {include file=$tpl_include}
        </div>
    {/if}

    <div class="buttonContainer table-block__holder table-block__footer-buttons">
        <button class="btn btn-danger deleteAllWork">{l s='Delete all' mod='ebay'}</button>
        <button class="btn btn-danger deleteSelectedWork">{l s='Delete selected' mod='ebay'}</button>
    </div>
</div>

{literal}
	<script>
        $(document).on('click', '.navPaginationListLogWorkersTab .pagination span', function(){
            var page = $(this).attr('value');
            if(page){
                $.ajax({
                    url: apilogs_ajax_link+'&configure=ebay',
                    data: {
                        ajax: true,
                        action: 'PaginationLogWorkerTab',
                        id_employee: id_employee,
                        profile: id_ebay_profile,
                        page: page
                    },
                    beforeSend : function(){
                        $('#contentLogWorkersTab').empty();
                        var html = '<div class="ajaxLoadingFormSyncTab" style="position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px;"></div>';
                        $('#contentLogWorkersTab').append(html);
                    },
                    success: function(data)
                    {
                        $('#contentLogWorkersTab').parent().html(data);
                    }
                });
            }
        });
        $(document).on('click', '.loadListWorker', function(){
            $.ajax({
                url: apilogs_ajax_link+'&configure=ebay',
                data: {
                    ajax: true,
                    action: 'PaginationLogWorkerTab',
                    id_employee: id_employee,
                    profile: id_ebay_profile,
                    page: 1
                },
                beforeSend : function(){
                    $('#contentLogWorkersTab').empty();
                    var html = '<div class="ajaxLoadingFormSyncTab" style="position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px;"></div>';
                    $('#contentLogWorkersTab').append(html);
                },
                success: function(data)
                {
                    $('#contentLogWorkersTab').parent().html(data);
                }
            });
        });

        $(document).on('click', '.workAllCheck', function(){
            var state = $(this).prop('checked');
            $('.workCheck').prop('checked', state);
        });

        $(document).on('click', '.deleteAllWork', function(){
            deleteWorkTasksByIds('all');
        });

        $(document).on('click', '.deleteSelectedWork', function(){
            var ids = [];
            $('.workCheck:checked').each(function(){
                ids.push($(this).attr('value'));
            });
            deleteWorkTasksByIds(ids);
        });

        function deleteWorkTasksByIds(ids_tasks) {
            if (ids_tasks == 'all' || ids_tasks.length != 0){
                $.ajax({
                    url: apilogs_ajax_link+'&configure=ebay',
                    data: {
                        ajax: true,
                        action: 'DeleteTasks',
                        ids_tasks: ids_tasks,
                        type: 'work'
                    },
                    success : function(data){
                        if(ids_tasks == 'all'){
                            $('#contentLogWorkersTab .tableDnD tbody').empty();
                            $('#contentLogWorkersTab .navPaginationListLogWorkersTab').empty();
                        } else{
                            $('.workCheck').each(function(){
                                if (ids_tasks.indexOf($(this).attr('value')) != -1){
                                    $(this).closest('tr').remove();
                                }
                            });
                        }

                    }
                })
            }

        }

	</script>
{/literal}



{* Bootstrap tooltip *}
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

