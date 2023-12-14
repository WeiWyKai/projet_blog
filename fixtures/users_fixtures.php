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
$insertUser = $bdd->prepare(
    "INSERT INTO users(name, email, password) VALUES ( :name, :email, :password)"
);

//Génération d'utilisateurs
for($i=0; $i<10; $i++){
    $insertUser->bindvalue(':name', $faker->name);
    $insertUser->bindvalue(':email', $faker->unique()->email);
    $insertUser->bindvalue(':password', password_hash('secret', PASSWORD_DEFAULT));
    $insertUser-> execute();
}
