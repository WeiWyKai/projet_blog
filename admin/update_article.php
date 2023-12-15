<?php
/**
 * update_article.php
 * Mise à jour d'un article en BDD
 */

/*
 * Seule une personne connectée peut y accéder
 *Verifier si la methode du formulaire recue est bien "POST"
 *Connexion à la BDD
 *Recuperer et nettoyer les donnees
 *Mise à jour vers le formulaire d'édition avec un message de succès
  */


/**
 * Mettre à jour les catégories choisies dans le formulaire :
*- Une catégorie déjà liée ne doit pas être de nouveau insérée dans la table de relation ;
*- Une catégorie présélectionnée, mais retiré, doit disparaitre de la table des relations ;
*- Une nouvelle catégorie choisie doit être insérée dans la table des relations ;
 */

session_start();

if(!isset($_SESSION['user'])){
    header('Location: index.php');
    exit;
}

require_once '../connexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: dashboard.php');
    exit;
};
$bdd = connectBdd('root', '', 'blog_db');

// Récupérer et nettoyer les données
$id = $_GET['id'];
$title = htmlspecialchars(strip_tags($_POST['title']));
$content = htmlspecialchars(strip_tags($_POST['content']));
$categories = array_map('strip_tags', $_POST['categories']);

 //MaJ des categories liees a l'articles
$deleteQuery = $bdd-> prepare(
    "DELETE FROM articles_categories WHERE article_id = :id");
$deleteQuery->bindvalue(':id', $id);
$deleteQuery->execute();

$insertCategoryQuery= $bdd->prepare(
    "INSERT INTO articles_categories(article_id, category_id) VALUES (:article_id, :category_id)"
);
foreach ($categories as $category){
    $insertCategoryQuery->bindvalue(':article_id' , $id);
    $insertCategoryQuery->bindvalue(':category_id' , $category);
    $insertCategoryQuery->execute();
}

if(!empty($title) && !empty($content)){
    //MaJ titre et contenu de l'article dans la table "article
    $query = $bdd->prepare(
        "UPDATE articles SET title = :title, content =:content WHERE id=:id");
    $query->bindvalue(':title',$title);
    $query->bindvalue(':content',$content);
    $query->bindvalue(':id',$id);
    $query->execute();

   
    
    //Message de succès
    $_SESSION['success']= "Les modifications ont bien été prises en compte";

    }else{
        $_SESSION['error']= 'Le titre et le contenu sont obligatoires';
    }




 // Redirection vers le formulaire d'édition
 header("Location: edit.php?id=$id");
 exit;
