<?php

/** users_fixtures.php */

//Chargement des dépendances Composer
require_once '../vendor/autoload.php';

//Connexion à la BDD
require_once '../connexion.php'; 

$bdd = connectBdd('root', '', 'blog_db');

//Utilisation de la bibliothèque Faker
$faker =Faker\Factory::create();

//Préparation de la requête d'insertion d'utilisateur
$insertCategory = $bdd->prepare(
    "INSERT INTO categories(name) VALUES ( :name)"
);

//Génération de catégories
for($i=0; $i<8; $i++){
    $insertCategory->bindvalue(':name', $faker->word);
    $insertCategory-> execute();
}
