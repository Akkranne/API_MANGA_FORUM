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

// Requête pour récupérer les catégories de manga depuis la base de données
$sql = "SELECT c.id, c.categorie, c.img, COUNT(a.id) AS articlesCount
        FROM categorie c
        LEFT JOIN article a ON c.id = a.id_categorie
        GROUP BY c.id";
$result = $conn->query($sql);

$categories = [];

if ($result->num_rows > 0) {
    // Parcourir les résultats de la requête et construire le tableau des catégories avec le nombre total d'articles
    while ($row = $result->fetch_assoc()) {
        $category = [
            'id' => $row['id'],
            'categorie' => $row['categorie'],
            'img' => $row['img'],
            'articlesCount' => $row['articlesCount']
        ];
        $categories[] = $category;
    }
}

// Fermeture de la connexion à la base de données
$conn->close();

// Envoyer la réponse JSON des catégories de manga avec le nombre total d'articles
header('Content-Type: application/json');
echo json_encode($categories);
?>
