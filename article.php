<?php
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Max-Age: 86400');

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mangaforum";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion à la base de données: " . $conn->connect_error);
}

// Récupération de l'ID de l'article depuis la requête GET
$articleId = $_GET['id'];

$sql = "SELECT article.id, article.title, article.description, article.created_date, user.username 
        FROM article 
        INNER JOIN user ON article.id_user = user.id
        WHERE article.id = $articleId";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $articleData = [
        'id' => $row['id'],
        'title' => $row['title'],
        'content' => $row['description'],
        'created_date' => $row['created_date'],
        'username' => $row['username'],
    ];

    $sqlLikes = "SELECT COUNT(*) AS like_count
                 FROM like_post
                 WHERE id = $articleId AND like_post = 1";

    $resultLikes = $conn->query($sqlLikes);

    if ($resultLikes->num_rows > 0) {
        $rowLikes = $resultLikes->fetch_assoc();
        $likeCount = $rowLikes['like_count'];
        $articleData['like_count'] = $likeCount;
    } else {
        $articleData['like_count'] = 0;
    }

    echo json_encode($articleData);
} else {
    echo "Article non trouvé";
}

$conn->close();

?>
