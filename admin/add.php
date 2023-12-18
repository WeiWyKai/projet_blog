<?php

// Démarrer une session
session_start();

// Vérifie si l'utilisateur peut accéder à cette page
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Connexion à la base de données
require_once '../connexion.php';
$bdd = connectBdd('root', '', 'blog_db');



// Sélectionne toutes les catégories
$query = $bdd->query("SELECT * FROM categories");
$categories = $query->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editer un article</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body> 
    <!-- Message de succès -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Messages d'erreurs -->
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <a href="dashboard.php" class="p-3">Retour</a>
    <form action="add_article.php" method="post" enctype="multipart/form-data" class="p-5">
        <h1 class="bg-primary border rounded-3 p-2 text-light">Ajout d'un article</h1>
     

        <div class="mb-3">
            <label for="title" class="form-label" required>Titre</label>
            <input type="text" class="form-control" id="title" name="title" >
        <div class="mb-3">
            <label for="content" class="form-label">Contenu</label>
            <textarea rows="15" class="form-control" id="content" name="content" ></textarea>
        </div>
        <div class="mb-3">
            <label for="picture" class="form-label">Photo de couverture</label>
            <input type="file" class="form-control" id="picture" name="cover" >
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Catégories</label>
            <select name="categories[]" id="category" class="form-control" multiple>
            <?php 
                foreach ($categories as $categorie):?>
                    <option value="<?php echo $categorie['id']?>">
                      <?php echo $categorie['name']?>
                    </option>
                <?php endforeach; ?>
            </select>
            </div>
        
        <button type="submit" class="btn btn-success">Ajouter l'article</button>
    </form>
</body>
</html>