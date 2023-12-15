<?php
//Démarrer une session
session_start();

//Vérifie si l'ulisateur peut accéder à cette page
if(!isset($_SESSION['user'])){
    header('Location: index.php');
    exit;
}
require_once '../connexion.php';
$bdd = connectBdd('root', '', 'blog_db');



$query = $bdd->prepare("
    SELECT articles.id, articles.title, articles.publication_date, GROUP_CONCAT(categories.name SEPARATOR ', ') AS categories 
    FROM articles 
    LEFT JOIN articles_categories ON articles_categories.article_id = articles.id 
    LEFT JOIN categories ON categories.id = articles_categories.category_id 
    WHERE user_id= :id 
    GROUP BY articles.id;");
$query->bindvalue(':id' , $_SESSION['user']['id']);
$query->execute();

$articles =$query->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
 
</head>
<body>
    <h1>Dashboad</h1>
    <a href="logout.php" class="btn btn-danger">Déconnexion</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Catégories</th>
                <th>Date de publication</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articles as $article): ?>
            <tr>
                <td> <?php echo $article['id'] ?></td>
                <td> <?php echo $article['title'] ?> </td>
                <td> <?php echo $article['categories'] ?></td>
                <td>
                    <?php
                    $date = DateTime::createFromFormat('Y-m-d H:i:s', $article['publication_date']);
                    echo $date->format('d/m/Y H:i:s');
                                ?> 
                </td>
                <td> 
                    <a href="edit.php?id=<?php echo $article['id']; ?>" class="btn btn-primary">Editer</a> 
                    <a href="" class="btn btn-danger">Supprimer</a>  
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
 

</body>
</html>

