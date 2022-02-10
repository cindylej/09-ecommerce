<?php
require_once('../inc/init.inc.php');

if (!adminConnect())
{
  header('location: ' . URL . 'connexion.php');
}

//echo '<pre style="margin-left: 350px">'; print_r($_POST); echo'</pre>';
//echo '<pre style="margin-left: 350px">'; print_r($_FILES); echo'</pre>';

if (isset($_GET['action']) && $_GET['action'] == 'suppression') 
{
  //echo "<p style='margin-left: 350px;'>Je veux supprimer ce produits</p>";
  $delete = $bdd->prepare("DELETE FROM produit WHERE id_produit = :id_produit");

  $delete->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);

  $delete->execute();

  $_GET['action'] = 'affichage';

  $msg = "<p class='col-7 bg-success text-white text-center mx-auto p-3 mt-3'>L'article n° <strong>$_GET[id_produit]</strong> a été supprimé avec succès.</p>"; 





}

if (isset($_POST['reference'], $_POST['categorie'], $_POST['titre'], $_POST['description'],$_POST['couleur'], $_POST['taille'], $_POST['public'], $_POST['prix'], $_POST['stock'])
) 
{
    $photoBdd = '';
    if (isset($_GET['action']) && $_GET['action'] == 'modification')
    {
      $photoBdd = $_POST['photo_actuelle'];
    }

   if (!empty($_FILES['photo']['name'])) 
   {
       $nomPhoto = $_POST['reference'] . '-' . $_FILES['photo']['name'];
       //echo"<p style='margin-left: 350px'>$photoDossier</p><hr>";

       $photoBdd = URL . "assets/uploads/$nomPhoto";

    $photoDossier = RACINE_SITE . "assets/uploads/$nomPhoto";

       copy($_FILES['photo']['tmp_name'], $photoDossier);
   }

   if (isset(_GET['action']) && $_GET['action'] == 'ajout')
  {
     $data = $bdd->prepare("INSERT INTO produit (reference, categorie, titre, description, couleur, taille, photo, public, prix, stock) VALUES (:reference, :categorie, :titre, :description, :couleur, :taille, :photo, :public, :prix, :stock)");

     $_GET['action'] = 'affichage';

     $msg = "<p class='col-7 bg-success text-white text-center mx-auto p-3 mt-3'> Le produit référence <strong>$_POST[reference]</strong> a été enregistré avec succès.</p>";
  }
  elseif(isset($_GET['action']) && $_GET['action'] == 'modification')
  {
    $data = $bdd->prepare("UPDATE produit SET reference = :reference, categorie = :categorie, titre = :titre, description = :description, couleur = :couleur, taille = :taille, photo = :photo, public = :public, prix = :prix, stock = :stock WHERE id_produit = :id_produit");

    $data->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
  }

  $_GET['action'] = 'affichage';

  $msg = "<p class='col-7 bg-success text-white text-center mx-auto p-3 my-3'> L'article référence'>$_POST[reference]</strong> a été modifier avec succès.</p>";

   $data->bindValue(':reference', $_POST['reference'], PDO::PARAM_STR);
   $data->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
   $data->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
   $data->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
   $data->bindValue(':couleur', $_POST['couleur'], PDO::PARAM_STR);
   $data->bindValue(':taille', $_POST['taille'], PDO::PARAM_STR);
   $data->bindValue(':photo', $photoBdd, PDO::PARAM_STR);
   $data->bindValue(':public', $_POST['public'], PDO::PARAM_INT);
   $data->bindValue(':prix', $_POST['prix'], PDO::PARAM_STR);
   $data->bindValue(':stock', $_POST['stock'], PDO::PARAM_STR);

    $data->execute();
    
}

if (isset($_GET['action']) && $_GET['action'] == 'modification')
{
  //echo "<p style='margin-left: 350px;'>Je veux modifier ce produit</p>";
  $update = $bdd->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
  $update->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
  $update->execute();

  $produitActuel = $update->fetch(PDO::FETCH_ASSOC);
  echo'<pre>'; print_r($produitActuel); echo '</pre>';

  $reference = (isset($produitActuel['reference'])) ? $produitActuel['reference'] : '';
  //echo"<p style='margin-left: 350px'>$reference</p>";

  $reference = (isset($produitActuel['reference'])) ? $produitActuel['reference'] : '';
  $categorie = (isset($produitActuel['categorie'])) ? $produitActuel['categorie'] : '';
  $titre = (isset($produitActuel['titre'])) ? $produitActuel['titre'] : '';
  $description = (isset($produitActuel['description'])) ? $produitActuel['description'] : '';
  $couleur = (isset($produitActuel['couleur'])) ? $produitActuel['couleur'] : '';
  $taille = (isset($produitActuel['taille'])) ? $produitActuel['taille'] : '';
  $photo = (isset($produitActuel['photo'])) ? $produitActuel['photo'] : '';
  $public = (isset($produitActuel['public'])) ? $produitActuel['public'] : '';
  $prix = (isset($produitActuel['prix'])) ? $produitActuel['prix'] : '';
  $stoct = (isset($produitActuel['stock'])) ? $produitActuel['stock'] : '';
}

require_once('../inc/inc_back/header.inc.php');
require_once('../inc/inc_back/nav.inc.php');
?>

<!-- 
Exo : afficher sous forme de tableau de HTML l'ensemble des produits stockés en BDD
1. requete de selection (query)
2. Afficher le nombre de produit selectionnés en BDD (rowCount())
3. Récupérer les informations sous forme de tableau (fetchAll)
4. Déclarer le tableau HTML (<table>)
5. Afficher les entêtes du tableau (<th>) en passant par le résultat du fetchAll()
6. Afficher tout les produits de la BDD à l'aide de boucle (foreach) dans des lignes (<tr>) et cellules (<td>) du tableau 
7. Prévoir un lien de modification / suppression pour chaque produit dans le tableau HTML
-->

<div class="mt-3 text-center">
    <a href="?action=ajout" class="btn btn-secondary">Nouvel article</a>
    <a href="?action=affichage" class="btn btn-secondary">Affichage des articles</a>
</div>



<?php if(isset($_GET['action']) && $_GET['action'] == 'affichage'): ?>
  
<h1 class="text-center my-5">Affichages produits</h1>

<?php

if (isset($msg)) echo $msg; 
 

$data = $bdd->query("SELECT * FROM produit");
echo '<p><span class="badge bg-success">' . $data->rowCount() . '</span> article(s) enregistrés.</p>';
$products = $data->fetchAll(PDO::FETCH_ASSOC);
echo '<pre>'; print_r($products); echo '</pre>';

echo '<table class="table table-bordered"><tr>';

foreach ($products[0] as $key => $value) {
  echo "<th class='text-center'>" . ucfirst($key) . "</th>";
}
echo "<th class='text-center'>Actions</th>";

echo '</tr>';

foreach ($products as $key => $tab) 
{
  echo '<tr>';
  foreach ($tab as $key2 => $value)
  { 
    if ($key2 == 'photo') 
      echo "<td><img src='$value' alt='$tab[titre]' class='img-products'></td>";

    elseif($key2 == 'couleur')
      echo "<td style='background-color: $value;' class='text-white'>$value</td>";  

    elseif($key2 == 'prix')
      echo "<td><strong>" . $value ."€</strong></td>";

    elseif($key2 == 'description')
      echo "<td>$value</td>";
      
    else
    echo "<td class='text-center'>$value</td>"; 
    
  }
  echo '<td>';
    echo "<a href='?action=modification&id_produit=$tab[id_produit]' class='btn btn-primary mb-3'><i class='bi bi-pencil-square'></i></a>";
    echo "<a href='?action=suppression&id_produit=$tab[id_produit]' class='btn btn-dark' onclick='return(confirm(\"En êtes vous certain ?\"))';><i class='bi bi-trash'></i></a>";
  echo '</td>';

  echo '</tr>';
  
}

echo '</table>';

endif;


if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) : 
 

?>


<h1 class="text-center my-5"><?= ucfirst($_GET['action']) ?> Article</h1>

<?php if (isset($validInsert)) echo $validInsert; ?>


<form method="post" enctype="multipart/form-data" class="row g-3">
  <div class="col-md-6">
    <label for="reference" class="form-label">Réference</label>
    <input type="text" class="form-control" id="reference" name="reference" value="<?php if(isset($reference)) echo $reference; ?>">
    
  </div>

  <div class="col-md-6">
    <label for="categorie" class="form-label">Catégorie</label>
    <input type="categorie" class="form-control" id="categorie" name="categorie" value="<?php if(isset($categorie)) echo $categorie; ?>">
   
  </div>

  <div class="col-12">
    <label for="titre" class="form-label">Titre</label>
    <input type="text" class="form-control" id="titre" name="titre" value="<?php if(isset($titre)) echo $titre; ?>">
    
  </div>

  <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea type="text" class="form-control" id="description" name="description" rows="10"><?php if(isset($description)) echo $description; ?></textarea>
    </div>

  <div class="col-4">
    <label for="couleur" class="form-label">Couleur</label>
    <input type="color" class="form-control input-couleur" id="couleur" name="couleur" value="<?php if(isset($couleur)) echo $couleur; ?>">
  </div>

  <div class="col-4">
    <label for="taille" class="form-label">Taille</label>
    <select id="taille" name="taille" class="form-select">

      <option value="s"<?php if(isset($taille) && $taille == 's') echo 'selected'; ?>>S</option>

      <option value="m"<?php if(isset($taille) && $taille == 'm') echo 'selected'; ?>>M</option>

      <option value="l"<?php if(isset($taille) && $taille == 'l') echo 'selected'; ?>>L</option>

      <option value="xl"<?php if(isset($taille) && $taille == 'xl') echo 'selected'; ?>>XL</option>

    </select>
  </div>

  <div class="col-4">
    <label for="public" class="form-label">Public</label>
    <select id="public" name="public" class="form-select">

    <option value="homme"<?php if(isset($public) && $public == 'homme') echo 'selected'; ?>>Homme</option>

      <option value="femme"<?php if(isset($public) && $public == 'femme') echo 'selected'; ?>>Femme</option>

      <option value="mixte"<?php if(isset($mixte) && $mixte == 'mixte') echo 'selected'; ?>>Mixte</option>

    </select>
  </div>

  <div class="col-md-4">
        <label for="photo" class="form-label">Photo</label>
        <input type="file" class="form-control" id="photo" name="photo">
        <input type="hidden" id="photo_actuelle" name="photo_actuelle" value="<?php if(isset($photo)) echo $photo; ?>" >
    </div>

  <div class="col-4">
    <label for="prix" class="form-label">Prix</label>
    <input type="text" class="form-control" id="prix" name="prix" value="<?php if(isset($prix)) echo $prix; ?>">
  </div>

  <div class="col-4">
    <label for="stock" class="form-label">Stock</label>
    <input type="text" class="form-control" id="stock" name="stock" value="<?php if(isset($stock)) echo $stock; ?>">
  </div>

  <?php if(isset($photo) && !empty($photo)): ?> 
   
  <div clas="col-7 mx-auto d-flex flex-column align-items-center rounded shadow-sm border">
  
  <small class="fst-italic mt-3"> photo actuelle de l'article. Vous pouvez uploader une nouvelle photo si vous souhaitez la modifier. </small>

  <img src="<?= $photo ?>" alt="" class="img-product-update">

  </div>

  <?php endif; ?>

  <div class="col-12">
   <button type="submit" class="btn btn-dark mb-5"><?= ucfirst($_GET['action']) ?> action</button>
  </div>
</form>
   
<?php
endif;
require_once('../inc/inc_back/footer.inc.php');