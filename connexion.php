<?php

/**
 * Connexin à la base de données
 */

 function connectBdd(
    string $user , //Nom d'utilisateur pour se connecter à la BDD
    string $Password, //MdP pour se co à la BDD
    string $database, // Nom de la BDD avec laquelle on souhaite travailler
    string $host= "localhost" //localisation de la BDD
    ){
        /**
         * On utilisera la classe PHP "PDO" (PHP Data Object)
         */
    try{
        $bdd = new PDO(
            "mysql:host=$host;dbname=$database",
            $user,
            $Password,
            [
                //Gestion des erreurs SQL
                PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
                //Gestion des jeux de caratères
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                //Choix du retour des résultats
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }catch(Exception $exception){
        throw new Exception(
            "La connexion à la base de donnée a échoué: {$exception->getMessage()}"
        );
    }
    return $bdd;
 }