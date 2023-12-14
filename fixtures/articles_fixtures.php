<?php

/** articles_fixtures.php */

//Chargement des dépendances Composer
require_once '../vendor/autoload.php';

//Connexion à la BDD
require_once '../connexion.php'; 

$bdd = connectBdd('root', '', 'blog_db');

//Utilisation de la bibliothèque Faker
$faker =Faker\Factory::create();

//Préparation de la requête d'insertion d'utilisateur
$insertArticle = $bdd->prepare(
    "INSERT INTO articles(title, content, cover, publication_date, user_id) VALUES ( :title, :content, :cover, :publication_date, :user_id)"
);

//Sélection de tous les utilisateurs
$query = $bdd->query("SELECT*FROM users");
$users =$query->fetchAll();

//Génération d'articles
for($i=0; $i<50; $i++){

    //Sélection d'un utilisateur aléatoirement 
    $user = $faker-> randomElement($users);

    //Géneration de date 
    $date= $faker->dateTimeBetween('-2 years')->format('Y-m-d H:i:s');

    $insertArticle->bindvalue(':title', $faker->sentence(3));
    $insertArticle->bindvalue(':content', $faker->paragraphs(2, true));
    $insertArticle->bindvalue(':cover',$faker->imageUrl(640, 480, 'animals', true));
    $insertArticle->bindvalue(':publication_date',$date);
    $insertArticle->bindvalue(':user_id',$user['id']);

    $insertArticle-> execute();
}
