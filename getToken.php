<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: GET');

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

// Récupérer le nom d'utilisateur depuis la requête GET
$username = isset($_GET['username']) ? $_GET['username'] : '';

if ($username) {
    // Récupérer le token associé au compte utilisateur depuis la base de données
    $stmt = $conn->prepare("SELECT token FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response = [
            'success' => true,
            'token' => $row['token']
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Le token n\'a pas pu être récupéré.'
        ];
    }

    $stmt->close();
} else {
    $response = [
        'success' => false,
        'message' => 'Veuillez fournir le nom d\'utilisateur.'
    ];
}

// Fermeture de la connexion à la base de données
$conn->close();

// Retourner la réponse en tant que JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
