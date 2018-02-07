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

<div id="ebayOrphanListing">
    <div class="text-center">
        <button class="btn btn-default loadListOrphan">
            <i class="icon-eye"></i>
            <span>{l s='See orphan listings' mod='ebay'}</span>
        </button>
    </div>
</div>


<script type="text/javascript">
    // <![CDATA[
    var ebayOrphanListingsController = "{$ebayOrphanListingsController|addslashes}";
    var id_employee={$id_employee|escape:'htmlall':'UTF-8'};
    var profile={$id_ebay_profile|escape:'htmlall':'UTF-8'};
    var content_ebay_listings = $("#ebayOrphanListing button.loadListOrphan");
    content_ebay_listings.bind('click',  function(){
        $.ajax({
            type: "POST",
            url: ebayOrphanListingsController,
            data: "ajax=true&action=LoadTableOrphanListings&id_employee="+ id_employee +"&profile=" + id_ebay_profile,
            success: function(data)
            {
                $('#ebayOrphanListing').fadeOut(400, function(){
                    $(this).html(data).fadeIn();
                })
            }
        });
    });
    //]]>


    var orphan_listings_ebay_l = {ldelim}
        'Remove this ad?': "{l s='End this listing?' mod='ebay'}"
        {rdelim};


</script>

<script type="text/javascript" src="{$_module_dir_|escape:'htmlall':'UTF-8'}ebay/views/js/orphanListings.js?date={$date|escape:'htmlall':'UTF-8'}"></script>
