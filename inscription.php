<?php
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Max-Age: 86400');


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

function generateToken($username) {
    $timestamp = time();
    $pseudo = base64_encode($username);
    $timestampEncoded = base64_encode($timestamp);
    $randomDigits = mt_rand(100000, 999999);
    $randomLetters = bin2hex(random_bytes(7));
    $salt = 'sardoche'; 
    $token = $timestampEncoded . $pseudo . 'SAR_DO_CHE' . $randomDigits . $randomLetters . $timestampEncoded;
    $saltedToken = $salt . $token;
    $hashedToken = hash('sha256', $saltedToken);
    return $hashedToken;
}







// Récupérer les données d'inscription provenant de la requête POST
$data = json_decode(file_get_contents("php://input"), true);
$username = isset($data['username']) ? $data['username'] : '';
$email = isset($data['email']) ? $data['email'] : '';
$password = isset($data['password']) ? md5($data['password']) : ''; // Chiffre le mot de passe en MD5
$permission = 0;
$hashedToken = generateToken($username); // Générer un token basé sur le pseudo


// Vérifier si les données d'inscription sont présentes
if ($username && $email && $password) {
    // Vérifier si l'username existe déjà
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // L'username existe déjà, refuser l'inscription
        $response = [
            'success' => false,
            'message' => 'L\'username existe déjà. Veuillez choisir un autre username.'
        ];
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // L'email existe déjà, refuser l'inscription
            $response = [
                'success' => false,
                'message' => 'L\'email existe déjà. Veuillez choisir une autre adresse email.'
            ];
        } else {
            // Insérer les données dans la table "user"


            $stmt = $conn->prepare("INSERT INTO user (username, email, password, permission, token) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssis", $username, $email, $password, $permission, $hashedToken);

            if ($stmt->execute()) {
                $response = [
                    'success' => true,
                    'message' => 'Inscription réussie!'
                ];
                // Rediriger vers la page de connexion
                exit;
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Erreur lors de l\'inscription: ' . $stmt->error
                ];
            }
        }
    }

    $stmt->close();
} else {
    $response = [
        'success' => false,
        'message' => 'Veuillez fournir toutes les données d\'inscription.'
    ];
}

// Fermeture de la connexion à la base de données
$conn->close();

// Retourner la réponse en tant que JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
