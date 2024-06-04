<?php
//Include Configuration File
include('config.php');

$login_button = '';

// Verificar si hay un código de autorización de Google
if (isset($_GET["code"])) {

    $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);
    if (!isset($token['error'])) {

        $google_client->setAccessToken($token['access_token']);

        $_SESSION['access_token'] = $token['access_token'];

        $google_service = new Google_Service_Oauth2($google_client);

        $data = $google_service->userinfo->get();

        // Verificar si el correo electrónico es del dominio permitido
        if (strpos($data['email'], '@zapopan.tecmm.edu.mx') !== false) {
            // Almacenar la información del usuario en la sesión
            $_SESSION['user_first_name'] = $data['given_name'];
            $_SESSION['user_last_name'] = $data['family_name'];
            $_SESSION['user_email_address'] = $data['email'];

            // Insertar datos del usuario en la base de datos si no existe
            $first_name = $conn->real_escape_string($_SESSION['user_first_name']);
            $last_name = $conn->real_escape_string($_SESSION['user_last_name']);
            $email = $conn->real_escape_string($_SESSION['user_email_address']);

            $sql = "SELECT * FROM usuarios WHERE email = '$email'";
            $result = $conn->query($sql);

            if ($result->num_rows == 0) {
                $sql = "INSERT INTO usuarios (first_name, last_name, email) VALUES ('$first_name', '$last_name', '$email')";
                $conn->query($sql);
            }

            // Redirigir al usuario a la página de bienvenida
            header("Location: inicio.php");
            exit();
        } else {
            // Correo electrónico no permitido, redirigir a una página de error o mostrar un mensaje
            header("Location: error.php");
            exit();
        }
    }
}

// Crear el botón de inicio de sesión con Google si no hay un token de acceso en la sesión
if (!isset($_SESSION['access_token'])) {
    $login_button_google = '<a href="' . $google_client->createAuthUrl() . '" class="btn btn-primary btn-block mt-3">Registrate o inicia sesion</a>';
}

?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Its zapopan</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-4">
                <div class="card p-4 bg-dark text-white">
                    <h2 class="text-center mb-4">Registrate</h2>
                    <?php
                    if (!isset($_SESSION['access_token'])) {
                        if (isset($login_button_google)) {
                            echo '<div>' . $login_button_google . '</div>';
                        }
                    } else {
                        // Redirigir al usuario a la página de bienvenida si ya está autenticado
                        header("Location: inicio.php");
                        exit();
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
