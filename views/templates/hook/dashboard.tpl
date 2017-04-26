{* Conseils *}
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