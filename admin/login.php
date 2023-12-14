<?php
//Démarrage de la session
//Doit être placé au plus haut possible dans le code
session_start();
/**
 * login.php
 * Permet de vérifier si un utilisateur peut accéder à l'administration
 */

/**
 *Logique : 
*Vérifier si le formulaire est complet -> sinon erreur
*Nettoyer les données issues du formulaire
*Sélectionner l'utilisateur en BDD via son email -> sinon erreur
*Vérifier si le mot de passe du formulaire correspond à celui en BDD -> sinon erreur
*Rédiriger l'utilisateur vers la page "dashboard.php"
*/
require_once '../connexion.php'; 
$error = null;

//*Vérifier si le formulaire est complet 

if (!empty($_POST["email"]) && !empty($_POST["password"])){
//Nettoyer les données issues du formulaire
    $email = trim(htmlspecialchars(strip_tags($_POST['email'])));
    $password = htmlspecialchars(strip_tags($_POST['password']));

//Sélectionner l'utilisateur en BDD via son email

    $bdd = connectBdd('root', '', 'blog_db');

    $query = $bdd->prepare(
        "SELECT * FROM users WHERE email= :email");
    $query ->bindvalue(':email', $email);
    $query ->execute();

        /**
         * fetch() retourne un tableau associatif contenant soit:
         * -les infos d'un utilisateur
         * -false
         */
    $user = $query->fetch();

    //Vérifier si le MdP du formulaire correspond à celui en BDD
    if ($user && password_verify($password, $user['password'])){
        //Stocker les infos de l'utilisateur en session
        $_SESSION['user'] = $user;

        //Redirection vers le fichier "dashboard.php
        header('location: dashboard.php');
        exit;


    }else{
        $error = 'Identifiants invalides';
    }

}else{
    $error = 'Veuillez remplir tous les champs';
}

if ($error !== null) {

    //Déclaration d'une session contenant l'erreur
    $_SESSION['error'] = $error;
    header('location: index.php');
    exit;
}

?>

<div class="container p-3">
        <?php if ($error !== null) { ?>
            <div class="alert alert-danger">
                <?php echo $error; ?> -
                <a href="index.php">Retour</a>
            </div>
        <?php } ?>
    </div>