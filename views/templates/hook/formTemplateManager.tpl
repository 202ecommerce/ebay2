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
<form action="{$action_url|escape:'htmlall':'UTF-8'}" method="post" class="form form-horizontal panel" id="configForm3">
	<fieldset>
		<h4>{l s='Optimise your listing titles with tags:' mod='ebay'}</h4>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Title format' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<input class="form-control" placeholder="{l s='{Brand} Shirt, Size' mod='ebay'}" type="text" name="ebay_product_template_title" id="ebay_product_template_title" value="{$ebay_product_template_title|escape:'htmlall':'UTF-8'}">
			</div>

			<div class="col-sm-9 col-sm-push-3">
				<div class="help-block">
					{l s='Set up a title format using tags' mod='ebay'}<br>
					{l s='E.g. Blue Paul Smith Shirt, Size 10' mod='ebay'}
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Add tag' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<select id="selectTagTemplateTitle" class="form-control" style="width: auto;">
					<option selected disabled>{l s='Add tag' mod='ebay'}</option>
					<option value="{ldelim}TITLE{rdelim}">{ldelim}TITLE{rdelim}</option>
					<option value="{ldelim}BRAND{rdelim}">{ldelim}BRAND{rdelim}</option>
					<option value="{ldelim}REFERENCE{rdelim}">{ldelim}REFERENCE{rdelim}</option>
					<option value="{ldelim}EAN{rdelim}">{ldelim}EAN{rdelim}</option>
					{foreach from=$features_product item=feature}
						<option value="{ldelim}FEATURE_{$feature.name|upper|replace:' ':'_'|trim|escape:'htmlall':'UTF-8'}{rdelim}">{ldelim}FEATURE_{$feature.name|upper|replace:' ':'_'|trim|escape:'htmlall':'UTF-8'}{rdelim}</option>
					{/foreach}
				</select>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<hr>
		<h4><span data-dialoghelp="#tagsTemplate" data-inlinehelp="{l s='View the list of tags.' mod='ebay'}">{l s='Design an eye-catching template to attract buyers.' mod='ebay'} :</span></h4>

		<div class="alert alert-info alert-no-icon">
			<p><b>{l s='Use this template to choose how you’d like your products to appear on eBay.' mod='ebay'}</b></p>
			<ul style="padding-left:15px;">
				<li>{l s='Stand out from the crowd.' mod='ebay'}</li>
				<li>{l s='Make sure your description includes all the information the buyer needs to know.' mod='ebay'}</li>
				<li>{l s='Use the same words that the buyer would use to search.' mod='ebay'}</li>
			</ul>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Ebay product template' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<textarea style="width: 100%" class="rte" cols="100" rows="100" name="ebay_product_template">{$ebay_product_template|ebayHtml}</textarea>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">
				{l s='Add tag' mod='ebay'}
			</label>
			<div class="col-sm-9">
				<select id="selectTagTemplate" class="form-control" style="width: auto;">
					<option selected disabled>{l s='Add tag' mod='ebay'}</option>
					<option value="{ldelim}MAIN_IMAGE{rdelim}">{ldelim}MAIN_IMAGE{rdelim}</option>
					<option value="{ldelim}MEDIUM_IMAGE_1{rdelim}">{ldelim}MEDIUM_IMAGE_1{rdelim}</option>
					<option value="{ldelim}MEDIUM_IMAGE_2{rdelim}">{ldelim}MEDIUM_IMAGE_2{rdelim}</option>
					<option value="{ldelim}MEDIUM_IMAGE_3{rdelim}">{ldelim}MEDIUM_IMAGE_3{rdelim}</option>
					<option value="{ldelim}PRODUCT_PRICE{rdelim}">{ldelim}PRODUCT_PRICE{rdelim}</option>
					<option value="{ldelim}PRODUCT_PRICE_DISCOUNT{rdelim}">{ldelim}PRODUCT_PRICE_DISCOUNT{rdelim}</option>
					<option value="{ldelim}DESCRIPTION_SHORT{rdelim}">{ldelim}DESCRIPTION_SHORT{rdelim}</option>
					<option value="{ldelim}DESCRIPTION{rdelim}">{ldelim}DESCRIPTION{rdelim}</option>
					<option value="{ldelim}FEATURES{rdelim}">{ldelim}FEATURES{rdelim}</option>
					<option value="{ldelim}EBAY_IDENTIFIER{rdelim}">{ldelim}EBAY_IDENTIFIER{rdelim}</option>
					<option value="{ldelim}EBAY_SHOP{rdelim}">{ldelim}EBAY_SHOP{rdelim}</option>
					<option value="{ldelim}SLOGAN{rdelim}">{ldelim}SLOGAN{rdelim}</option>
					<option value="{ldelim}PRODUCT_NAME{rdelim}">{ldelim}PRODUCT_NAME{rdelim}</option>
				</select>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-9 col-sm-push-3">
				<a id="previewButton" class="btn btn-default">
					<i class="icon-eye"></i>
					<span>
						{l s='Preview' mod='ebay'}
					</span>
				</a>
				<a href="{$action_url|escape:'htmlall':'UTF-8'}&reset_template=1" class="btn btn-warning" onclick="return confirm('{l s='Are you sure?' mod='ebay'}');">
					<i class="icon-refresh"></i>
					<span>{l s='Reset template' mod='ebay'}</span>
				</a>
			</div>
		</div>

		<script type="text/javascript">	
			var iso = '{$iso|escape:'htmlall':'UTF-8'}';
			var pathCSS = '{$theme_css_dir|escape:'htmlall':'UTF-8'}';
			var ad = '{$ad|escape:'htmlall':'UTF-8'}';
		</script>

		<script type="text/javascript" src="{$base_uri|escape:'htmlall':'UTF-8'}js/tiny_mce/tiny_mce.js"></script>

		<script type="text/javascript">
			setTimeout(function() {
		{literal}
			if(tinyMCE.majorVersion == 4)
			{
				tinyMCE.init({
					mode : "specific_textareas",
					theme : "advanced",
					skin:"cirkuit",
					editor_selector : "rte",
					editor_deselector : "noEditor",
					plugins : "safari,pagebreak,style,table,advimage,advlink,inlinepopups,media,contextmenu,paste,fullscreen,xhtmlxtras,preview,code",
					// Theme options
					theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
					theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
					theme_advanced_buttons3 : "",
					theme_advanced_buttons4 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : false,
			        content_css : pathCSS+"global.css",
					document_batinyse_url : ad,
					width: "600",
					height: "auto",
					font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
					elements : "nourlconvert,ajaxfilemanager",
					file_browser_callback : "ajaxfilemanager",
					entity_encoding: "raw",
					convert_urls : false,
			        language : iso,
			        setup: function (ed) {
				        ed.on('init', function(args) {
				            $('#selectTagTemplate').insertAfter('#mce_36-body');
				        });
				   }
				});
			}
			else
				tinyMCE.init({
					mode : "specific_textareas",
					theme : "advanced",
					skin:"cirkuit",
					editor_selector : "rte",
					editor_deselector : "noEditor",
					plugins : "safari,pagebreak,style,table,advimage,advlink,inlinepopups,media,contextmenu,paste,fullscreen,xhtmlxtras,preview",
					// Theme options
					theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
					theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
					theme_advanced_buttons3 : "",
					theme_advanced_buttons4 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : false,
			        content_css : pathCSS+"global.css",
					document_batinyse_url : ad,
					width: "600",
					height: "auto",
					font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
					elements : "nourlconvert,ajaxfilemanager",
					file_browser_callback : "ajaxfilemanager",
					entity_encoding: "raw",
					convert_urls : false,
			        language : iso,
			        setup : function(ed) {
				      ed.onInit.add(function(ed) {
				          $('#selectTagTemplate').appendTo('#ebay_product_template_toolbargroup');
				      });
				   }
					
				});

			function ajaxfilemanager(field_name, url, type, win) {
				var ajaxfilemanagerurl = ad+"/ajaxfilemanager/ajaxfilemanager.php";
				switch (type) {
					case "image":
						break;
					case "media":
						break;
					case "flash": 
						break;
					case "file":
						break;
					default:
						return false;
			}
		    tinyMCE.activeEditor.windowManager.open({
		        url: ajaxfilemanagerurl,
		        width: 782,
		        height: 440,
		        inline : "yes",
		        close_previous : "no"
		    },{
		        window : win,
		        input : field_name
		    });
		}
		{/literal}
		},1000);
		</script>
	</fieldset>

	<div id="buttonEbayShipping" class="panel-footer">
		<input class="primary button" name="submitSave" type="hidden" id="save_ebay_shipping" value="{l s='Save and continue' mod='ebay'}"/>
		<button class="btn btn-default pull-right" type="submit" id="save_ebay_shipping">
			<i class="process-icon-save"></i>
			{l s='Save' mod='ebay'}
		</button>
	</div>
</form>

<script type="text/javascript">
	$('#selectTagTemplate').bind('change', function(){
		tinyMCE.activeEditor.execCommand('mceInsertContent', false, $(this).val())
	});
	$('#previewButton').click(function(){
		$.ajax({
			type: 'POST',
			url : module_dir+"ebay/ajax/previewTemplate.php",
			data :{ message : tinyMCE.activeEditor.getContent(), id_lang : id_lang, token : ebay_token },
			success: function(data) {
				$.fancybox(data);
			}
		});
		return false;
	});

	$('#selectTagTemplateTitle').bind('change', function(){
		$('#ebay_product_template_title').val($('#ebay_product_template_title').val()+$(this).val())
	});
</script>	
