<?php
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
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

// Récupération des paramètres de l'URL
$articleId = $_GET['id'];
$token = $_GET['id_user'];

// Requête pour récupérer l'ID de l'utilisateur à partir du token
$sql = "SELECT id FROM user WHERE token = '$token'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userId = $row['id'];
    
    // Vérifier si un like existe déjà pour cet article et cet utilisateur
    $sqlCheck = "SELECT id, like_post FROM like_post WHERE id = '$articleId' AND id_user = '$userId'";
    $resultCheck = $conn->query($sqlCheck);
    
    if ($resultCheck->num_rows > 0) {
        $rowCheck = $resultCheck->fetch_assoc();
        $likeId = $rowCheck['id'];
        $likePost = $rowCheck['like_post'];
        
        if ($likePost == 0) {
            // Si like_post est 0, le mettre à 1
            $sqlUpdate = "UPDATE like_post SET like_post = 1 WHERE id = '$likeId'";
        } else {
            // Si like_post est déjà 1, le mettre à 0
            $sqlUpdate = "UPDATE like_post SET like_post = 0 WHERE id = '$likeId'";
        }
        
        if ($conn->query($sqlUpdate) === TRUE) {
            echo "Like mis à jour avec succès";
        } else {
            echo "Erreur lors de la mise à jour du like : " . $conn->error;
        }
    } else {
        // Insertion d'un nouveau like avec like_post à 1
        $sqlInsert = "INSERT INTO like_post (id, id_user, like_post) VALUES ('$articleId', '$userId', 1)";
        
        if ($conn->query($sqlInsert) === TRUE) {
            echo "Like ajouté avec succès";
        } else {
            echo "Erreur lors de l'ajout du like : " . $conn->error;
        }
    }
} else {
    echo "Utilisateur non trouvé";
}

$conn->close();
?>
