<?php

// Démarrer une session
session_start();

// Vérifie si l'utilisateur peut accéder à cette page
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// verifie si le parametre 'id' est présent et /ou non vide
if(empty($_GET['id'])){
    header('Location: dashboard.php');
    exit;
}

// Connexion à la base de données
require_once '../connexion.php';
$bdd = connectBdd('root', '', 'blog_db');

// Récupération de l'ID de l'article
$articleId = $_GET['id'];

// Sélection de l'article en BDD
$query = $bdd->prepare("SELECT * FROM articles WHERE id = :id");
$query->bindValue(':id', $articleId);
$query->execute();

// fetch() car je récupère qu'un seul article
$article = $query->fetch();

//Si aucun article n'existe avec cet Id, redirection vers dashboard.php
//Verifier que l'article selectionné appartient bien à l'utilisateur
if(!$article || $article['user_id'] !== $_SESSION['user']['id']){
    header('Location: dashboard.php');
    exit;
}

// Sélectionne toutes les catégories
$query = $bdd->query("SELECT * FROM categories");
$categories = $query->fetchAll();

// Sélectionne toutes les catégories liées à l'article
$query = $bdd->prepare("SELECT category_id FROM articles_categories WHERE article_id = :id");
$query->bindValue(':id', $articleId);
$query->execute();

/**
 * PDO::FETCH_COLUMN
 * Retourne un tableau indexé contenant les valeurs extraites de la requête SQL pour une seule colonne
 */
$articlesCategories = $query->fetchAll(PDO::FETCH_COLUMN);

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

    <a href="dashboard.php">Retour</a>
    <form action="./update_article.php?id=<?php echo$article['id']; ?>" method="post" enctype="multipart/form-data" class="p-5">
        <h1 class="bg-primary border rounded-3 p-2 text-light">Modification de l'article</h1>
     

        <div class="mb-3">
            <label for="title" class="form-label">Titre</label>
            <input type="text" class="form-control" id="title" name="title" value= "<?php echo $article['title'];?>">
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Contenu</label>
            <textarea rows="15" class="form-control" id="content" name="content" ><?php echo $article['content'];?></textarea>
        </div>
        <div class="mb-3">
            <label for="picture" class="form-label">Photo de couverture</label>
            <input type="file" class="form-control" id="picture" name="cover" >
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Catégories</label>
            <select name="categories[]" id="category" multiple>
            <?php 
                foreach ($categories as $categorie):?>
                    <option 
                        value="<?php echo $categorie['id']?>"
                        <?php echo in_array($categorie['id'], $articlesCategories) ? 'selected' : '' ?>>
                        <?php echo $categorie['name']?></option>
                <?php endforeach; ?>
            </select>
            </div>
        
        <button type="submit" class="btn btn-success">Valider les modifications</button>
    </form>
</body>
</html>