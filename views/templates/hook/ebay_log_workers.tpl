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



<div id="contentLogWorkersTab">
	<div class="buttonContainer">
		<button class="btn btn-default deleteAllWork">{l s='Delete all'}</button>
		<button class="btn btn-default deleteSelectedWork">{l s='Delete selected'}</button>
	</div>
	<table class="table tableDnD" cellpadding="0" cellspacing="0" width="90%">
		<thead>
		<tr class="nodrag nodrop">
			<th><input type="checkbox" class="workAllCheck"></th>
			<th>{l s='ID' mod='ebay'}</th>
			<th>{l s='Date' mod='ebay'}</th>
			<th>{l s='ID Product' mod='ebay'}</th>
			<th>{l s='ID Attribute' mod='ebay'}</th>
		</tr>
		</thead>
		<tbody>
		{if $tasks}
			{foreach from=$tasks item='task'}
				<tr>
					<td><input type="checkbox" class="workCheck" value="{$task.id|escape:'htmlall':'UTF-8'}"></td>
					<td>{$task.id|escape:'htmlall':'UTF-8'}</td>
					<td>{$task.date_add|escape:'htmlall':'UTF-8'}</td>
					<td>{$task.id_product|escape:'htmlall':'UTF-8'}</td>
					<td>{$task.id_product_attribute|escape:'htmlall':'UTF-8'}</td>
				</tr>
			{/foreach}
		{/if}
		<tr></tr>
		</tbody>
	</table>
    {if $pages_all >1}
		<div class="navPaginationListLogWorkersTab" style="display:flex; justify-content:center">
            {include file=$tpl_include}
		</div>
    {/if}
</div>

{literal}
	<script>
        $(document).on('click', '.navPaginationListLogWorkersTab .pagination span', function(){
            var page = $(this).attr('value');
            if(page){
                $.ajax({
                    type: "POST",
                    url: module_dir+'ebay/ajax/paginationLogWorkersTab.php',
                    data: "token="+ebay_token+"&id_employee="+ id_employee +"&profile=" + id_ebay_profile + "&page=" + page,
                    beforeSend : function(){
                        $('#contentLogWorkersTab').empty();
                        var html = '<div class="ajaxLoadingFormSyncTab" style="position:relative; height:60px"><img src="../modules/ebay/views/img/ajax-loader-small.gif" style="position:absolute; left:50%; width:60px;"></div>';
                        $('#ebayOrphanListing').append(html);
                    },
                    success: function(data)
                    {
                        $('#contentLogWorkersTab').html(data);
                    }
                });
            }


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
                    type : 'POST',
                    url : module_dir+'ebay/ajax/deleteTasks.php',
                    data : {
                        ids_tasks : ids_tasks,
                        type : 'work',
                    },
                    success : function(data){
                        if(ids_tasks == 'all'){
                            $('#contentLogWorkersTab').empty();
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

