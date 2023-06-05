<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: GET');

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mangaforum";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données: " . $conn->connect_error);
}

// Vérifier si le token est présent dans la requête GET
if(isset($_GET['token'])){
    $token = $_GET['token'];

    // Requête pour récupérer l'utilisateur associé au token
    $sql = "SELECT * FROM user WHERE token = '$token'";
    $result = $conn->query($sql);

    // Vérifier si un utilisateur a été trouvé
    if ($result->num_rows > 0) {
        $users = array();

        // Boucler à travers les résultats et les ajouter au tableau $users
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        // Renvoyer l'utilisateur au format JSON
        header('Content-Type: application/json');
        echo json_encode($users);
        exit;
    } else {
        // Aucun utilisateur trouvé pour le token donné
        echo "Aucun utilisateur trouvé pour le token donné.";
        exit;
    }
}

// Si aucun token n'a été fourni dans la requête GET, renvoyer tous les utilisateurs

// Requête pour récupérer tous les utilisateurs
$sql = "SELECT * FROM user";
$result = $conn->query($sql);

// Vérifier si des résultats ont été obtenus
if ($result->num_rows > 0) {
    $users = array();

    // Boucler à travers les résultats et les ajouter au tableau $users
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    // Renvoyer les utilisateurs au format JSON
    header('Content-Type: application/json');
    echo json_encode($users);
} else {
    echo "Aucun utilisateur trouvé.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
