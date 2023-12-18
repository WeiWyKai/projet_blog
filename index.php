<?php
session_start();

require_once './connexion.php';
$bdd = connectBdd('root', '', 'blog_db');

$query=$bdd->query("
  SELECT 
    articles.id, 
    articles.title, 
    articles.publication_date,
    articles.content,
    articles.cover,
    users.name as author, 
    GROUP_CONCAT(categories.name SEPARATOR ', ') AS categories 
  FROM articles 
  INNER JOIN users ON articles.user_id = users.id
  LEFT JOIN articles_categories ON articles_categories.article_id = articles.id 
  LEFT JOIN categories ON categories.id = articles_categories.category_id 
  GROUP BY articles.id;
  ORDER BY articles.publication_date DESC
");
$articles = $query->fetchAll();

// Créer un tableau de catégories en "explosant" la chaine de caractère créée par la requête SQL
$groupedArticles = [];
foreach ($articles as $key => $article) {
    $groupedArticles[$key] = $article;
    $groupedArticles[$key]['categories'] = explode(', ', $article['categories']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blog Project</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="text-center pt-5">
      <h1>Mon super blog</h1>
      <?php if(!isset($_SESSION['user'])){ ?>
        <a href="admin/login.php" class="btn btn-primary my-3">Connexion</a>
      <?php }else{?>
        <a href="admin/dashboard.php" class="btn btn-primary my-3">Mes articles</a>
        <a href="admin/logout.php" class="btn btn-danger my-3">Deconnexion</a>
      <?php }?>
  </div>

  <div class="container p-3 pt-5 d-flex flex-column justify-content-center">
      <?php foreach($groupedArticles as $article): ?>
          <article class="pb-5">
              <!-- Titre de l'article -->
              <h1><?php echo $article['title']; ?></h1>

              <!-- Informations sur l'article -->
              <small class="d-block text-secondary pb-2">
                  <?php
                      $createdAt = DateTime::createFromFormat('Y-m-d H:i:s', $article['publication_date']);
                      $date = $createdAt->format('d.m.Y');
                  ?>
                  Auteur : <?php echo $article['author']; ?> - Posté <?php echo $date; ?>
              </small>

              <!-- Image de couverture -->
              <?php if (file_exists("public/uploads/{$article['cover']}")): ?>
                  <img
                      src="<?php echo "public/uploads/{$article['cover']}" ?>"
                      alt="<?php echo $article['title']; ?>"
                      class="img-fluid rounded"
                  >
              <?php endif; ?>

              <!-- Catégories de l'article -->
              <ul class="py-2 list-unstyled d-flex gap-2">
                  <?php foreach ($article['categories'] as $category): ?>
                      <li>
                          <a href="#">
                              <span class="badge rounded-pill text-bg-light">
                                  <?php echo $category ?>
                              </span>
                          </a>
                      </li>
                  <?php endforeach; ?>
              </ul>

              <!-- Contenu tronqué de l'article -->
              <p><?php echo mb_strimwidth($article['content'], 0, 100, '...'); ?></p>

              <a href="article.php?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-primary">
                  Lire la suite...
              </a>
          </article>
      <?php endforeach; ?>
  </div>
</body>
</html>