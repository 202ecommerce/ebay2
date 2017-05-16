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

<div class="col-md-9">


<br>
<h4>Produits</h4>
    Nombre de catégories PrestaShop : {$nb_categories|escape:'UTF-8'}<br>
    Nombre de produit exclus : {$nb_products_exclu|escape:'UTF-8'}<br>
    Nombre prévu d'annonces : {$nb_annonces_prevu|escape:'UTF-8'}<br>
    Nombre d’annonces eBay : {$nb_products|escape:'UTF-8'}<br>
    Nombre d'erreurs : {$count_product_errors|escape:'UTF-8'}<br>
    Synchronisation : {$type_sync_product|escape:'UTF-8'}<br>
    En cours d'envoi :  {$nb_tasks|escape:'UTF-8'}<br>
<br>
<h4>Ventes internationales passives</h4>
    Nombre de pays : {$nb_country_shipping|escape:'UTF-8'} <br>
<br>
<h4>Commandes</h4>
    CA eBay 30 derniers jours : {$ca_total|escape:'UTF-8'} <br>
    Commandes en erreur : {$count_order_errors|escape:'UTF-8'}<br>
    Dernier import : {$date_last_import|escape:'UTF-8'} par {$type_sync_order|escape:'UTF-8'}<br>
</div>

