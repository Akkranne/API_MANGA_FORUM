<?php
// Configuration de la connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mangaforum";

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification des erreurs de connexion
if ($conn->connect_error) {
    die("Échec de la connexion à la base de données: " . $conn->connect_error);
}

// Récupération de l'ID de la catégorie depuis la requête GET
$categoryId = $_GET['id'];

// Requête pour récupérer le nom de la catégorie
$categoryQuery = "SELECT categorie FROM categorie WHERE id = $categoryId";
$categoryResult = $conn->query($categoryQuery);

// Vérification du résultat de la requête
if ($categoryResult->num_rows > 0) {
    $categoryRow = $categoryResult->fetch_assoc();
    $categoryName = $categoryRow['categorie'];
} else {
    $categoryName = '';
}

// Requête pour récupérer les articles associés à la catégorie avec le nom d'utilisateur correspondant
$articlesQuery = "SELECT article.*, user.username, user.img FROM article INNER JOIN user ON article.id_user = user.id WHERE article.id_categorie = $categoryId";
$articlesResult = $conn->query($articlesQuery);

$articles = [];

// Parcourir les résultats de la requête et construire le tableau des articles avec le nom d'utilisateur
if ($articlesResult->num_rows > 0) {
    while ($articleRow = $articlesResult->fetch_assoc()) {
        $articleId = $articleRow['id'];
        $sqlLikes = "SELECT COUNT(*) AS like_count
                     FROM like_post
                     WHERE id = $articleId AND like_post = 1";

        $resultLikes = $conn->query($sqlLikes);

        if ($resultLikes->num_rows > 0) {
            $rowLikes = $resultLikes->fetch_assoc();
            $likeCount = $rowLikes['like_count'];
            $articleRow['like_count'] = $likeCount;
        } else {
            $articleRow['like_count'] = 0;
        }

        $article = [
            'id' => $articleRow['id'],
            'title' => $articleRow['title'],
            'description' => $articleRow['description'],
            'created_date' => $articleRow['created_date'],
            'id_user' => $articleRow['id_user'],
            'username' => $articleRow['username'],
            'id_categorie' => $articleRow['id_categorie'],
            'like_count' => $articleRow['like_count'],
            'img' => $articleRow['img'],

        ];
        $articles[] = $article;
    }
}

// Fermeture de la connexion à la base de données
$conn->close();

// Création du tableau de données à retourner en JSON
$data = [
    'categoryName' => $categoryName,
    'articles' => $articles,
];

// Envoi de la réponse JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
