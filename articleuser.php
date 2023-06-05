<?php

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

// Requête pour récupérer l'utilisateur associé à l'ID de l'article
$sql = "SELECT username FROM user WHERE id = $articleId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userData = [
        'username' => $row['username'],
    ];
    echo json_encode($userData);
} else {
    echo "Utilisateur non trouvé";
}

$conn->close();

?>
