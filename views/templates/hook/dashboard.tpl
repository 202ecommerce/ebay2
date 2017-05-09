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
<div class="alert alert-info alert-no-icon">
    Le nombre d'articles synchronisés avec eBay est important, vous devriez <b>activer la tache cron</b> en allant dans l'onglet Configuration.
</div>

<div class="alert alert-danger alert-no-icon">
    Il est impératif d'activer bla bla <b>produit hors stock</b> bla bla bla ...
</div>

<br>
<h4>Produits</h4>
Nombre d’annonces eBay : {$nb_products}<br>
Produits en erreur : {$count_product_errors}<br>
Produits en attente :  {$nb_tasks}<br>
<br>
<h4>Ventes internationales passives</h4>
Nombre de sites eBay :<br>
<br>
<h4>Commandes</h4>
CA eBay 30 derniers jours : <br>
Commandes en erreur : {$count_order_errors}<br>
Dernier import : {$dernier_import_order}<br>
</div>

<div class="col-md-3">
    <div class="panel">
        Des chiffres clé ici ?
    </div>
</div>