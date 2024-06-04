<?php

$servername = "localhost"; // Cambia esto si tu servidor de base de datos no está en localhost
$username = "root"; // Tu nombre de usuario de MySQL
$password = "migs24sape"; // Tu contraseña de MySQL
$dbname = "Usuarios"; // El nombre de la base de datos que acabas de crear

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


//config.php

//Include Google Client Library for PHP autoload file
require_once 'vendor/autoload.php';

//Make object of Google API Client for call Google API
$google_client = new Google_Client();

//Set the OAuth 2.0 Client ID | Copiar "ID DE CLIENTE"
$google_client->setClientId('1079376404390-e81tr1t5108hl3pvfhinpl5gplgiq5lg.apps.googleusercontent.com');

//Set the OAuth 2.0 Client Secret key
$google_client->setClientSecret('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');

//Set the OAuth 2.0 Redirect URI | URL AUTORIZADO
$google_client->setRedirectUri('http://localhost/API/index.php');

// to get the email and profile 
$google_client->addScope('email');

$google_client->addScope('profile');



?>