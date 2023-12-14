
<?php
//Démarrer une session
session_start();

//Vérifie si l'ulisateur peut accéder à cette page
if(!isset($_SESSION['user'])){
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Dash</h1>
    <a href="logout.php">Déconnexion</a>
</body>
</html>

