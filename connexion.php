<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: POST');

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

// Récupérer les données d'identification provenant de la requête POST
$data = json_decode(file_get_contents("php://input"), true);
$username = isset($data['username']) ? $data['username'] : '';
$password = isset($data['password']) ? md5($data['password']) : '';

// Vérifier si les données d'identification sont présentes
if ($username && $password) {
    // Vérifier les informations d'identification dans la base de données
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Les informations d'identification sont valides, récupérer le token de l'utilisateur
        $row = $result->fetch_assoc();
        $token = $row['token'];

        $response = [
            'success' => true,
            'message' => 'Connexion réussie!',
            'token' => $token
        ];
    } else {
        // Les informations d'identification sont incorrectes
        $response = [
            'success' => false,
            'message' => 'Identifiants incorrects.'
        ];
    }

    $stmt->close();
} else {
    $response = [
        'success' => false,
        'message' => 'Veuillez fournir les informations d\'identification.'
    ];
}

// Fermeture de la connexion à la base de données
$conn->close();

// Retourner la réponse en tant que JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
