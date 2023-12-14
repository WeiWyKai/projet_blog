<?php

/**Index.php 
 * Généerer toutes les fixtures
 * 
 * Liste des fichiers afin de générer les jeux de données dans l'ordre d'insertion en BDD
*/

/**
 * http://PHP.test/projet_blog/fixtures/index.php?truncate=1
 * Si le paramètre "truncate" est présent dans l'URL, on vide nos tables SQL
 */

if(isset($_GET['truncate'])){
    //Connexion à la BDD
    require '../connexion.php';
    $bdd= connectBdd('root', '', 'blog_db');

    // REquête pour vider toutes les tables SQL
    // ATTENTION! L'ordre est IMPORTANT à casue des FK: Utiliser SET FOREIGN_KEY_CHECK!

    $bdd->query("
        SET FOREIGN_KEY_CHECKS =0;
        TRUNCATE articles_categories;
        TRUNCATE comments;
        TRUNCATE articles;
        TRUNCATE categories;
        TRUNCATE users;
        SET FOREIGN_KEY_CHECKS =1

    ");
}

require_once 'users_fixtures.php';
require_once 'categories_fixtures.php';
require_once 'articles_fixtures.php';
require_once 'comments_fixtures.php';
require_once 'articles_categories_fixtures.php';