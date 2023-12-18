<?php

/**
 * add-articles.php
 */

session_start();

if(!isset($_SESSION['user'])){
    header('Location: index.php');
    exit;
}

//Chargement des dependances PHP (pour le slugify)
require_once '../vendor/autoload.php';

require_once '../connexion.php';
$bdd = connectBdd('root', '', 'blog_db');

if ($_SERVER['REQUEST_METHOD']==='POST'){

  $title = htmlspecialchars(strip_tags($_POST['title']));
  $content = htmlspecialchars(strip_tags($_POST['content']));
  $cover = $_FILES['cover'];

  if(!empty($title && 
  !empty($content) && 
  !empty($_POST['categories']) && 
  isset($cover) && $cover['error'] === UPLOAD_ERR_OK 
  )){
    //Nettoyage des catégories
    $categories = array_map('strip_tags', $_POST['categories']);
    
    $typeExt = [
      'png' => 'image/png',
      'jpg' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'webp' => 'image/webp',
  ];

  $sizeMax = 1 * 1024 * 1024;
  $extension = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));

  // Vérifier si le fichier est bien une image autorisée
  if (array_key_exists($extension, $typeExt) && in_array($_FILES['cover']['type'], $typeExt)) {

    // Vérifie si le poids de l'image ne dépasse pas la limite fixée
    if ($_FILES['cover']['size'] <= $sizeMax) {

      //Inserer en BDD les données
      $queryNewArticle = $bdd -> prepare("
        INSERT INTO articles (title, content, cover, publication_date, user_id)
        VALUE (:title , :content, :cover, :publication_date, :user_id)
        ");

      $queryNewArticle->bindvalue(':title',$title);
      $queryNewArticle->bindvalue(':content',$content);
      $queryNewArticle->bindvalue(':cover',$cover['name']);
      $queryNewArticle->bindvalue(':publication_date',(new DateTime('now'))->format('Y/m/d H:i:s')); // recupe heure et date du jour et force le format de la date
      $queryNewArticle->bindvalue(':user_id',$_SESSION['user']['id']);
      $queryNewArticle->execute();

      //Recuperation de l'ID de l'article nouvellement créé
      $id =$bdd->lastInsertId();

      // Renomme le nom de l'image
      $slugify = new \Cocur\Slugify\Slugify();
      $newName = $slugify->slugify("$title-$id");
      $cover = "$newName.$extension";

      // Télécharge la nouvelle image sous le nouveau nom
      move_uploaded_file(
          $_FILES['cover']['tmp_name'],
          "../public/uploads/$cover"
      );

      /**
       * <Maj du nom de l'image dans la BDD
       * on met à jour le nom de l'image apres une insertion car notre image contient l'id de l'article que l'on nz peut pas connaitre au moment de l'insetion plus haut, donc cela doit se faire en 2tps
       */

      $queryUpdateCover=$bdd->prepare("UPDATE articles SET cover = :cover WHERE id =:id");
      $queryUpdateCover->bindvalue(':cover',$cover);
      $queryUpdateCover->bindvalue(':id',$id);
      $queryUpdateCover->execute();


       // Insertion dans la table de relation "articles_categories"
       $queryInsertRelationCategory = $bdd->prepare("
       INSERT INTO articles_categories (article_id, category_id) VALUES (:article_id, :category_id)
      ");

      foreach($categories as $category) {
          $queryInsertRelationCategory->bindValue(':article_id', $id);
          $queryInsertRelationCategory->bindValue(':category_id', $category);
          $queryInsertRelationCategory->execute();
      }

      $_SESSION['success'] = "Votre nouvel article a été correctement enregistré";

      header('Location: dashboard.php');
      exit;
      
      } else {
        $_SESSION['error'] = "L'image ne doit pas dépasser les 1Mo";
    }
  } else {
      $_SESSION['error'] = "Le fichier n'est pas une image conforme";
  }

  }else{
    $_SESSION['error']= 'Tous les champs sont obligatoires';
  }

}

header('Location: add.php');