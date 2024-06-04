<?php
session_start();
if (!isset($_SESSION['user_email_address'])) {
    // Redirigir a la página de inicio de sesión si no está autenticado
    header("Location: index.php");
    exit();
}

// Incluir archivo de configuración
include('config.php');

// Obtener el ID del usuario desde la base de datos
$email = $_SESSION['user_email_address'];
$sql = "SELECT id FROM usuarios WHERE email = '$email'";
$result = $conn->query($sql);
$user_id = $result->fetch_assoc()['id'];

// Obtener los detalles del usuario desde la base de datos
$sql = "SELECT numero_control, telefono FROM detalles_usuarios WHERE usuario_id = '$user_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $details = $result->fetch_assoc();
    $_SESSION['numero_control'] = $details['numero_control'];
    $_SESSION['telefono'] = $details['telefono'];
} else {
    $_SESSION['numero_control'] = '';
    $_SESSION['telefono'] = '';
}

// Actualizar datos del usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $numero_control = $conn->real_escape_string($_POST['numero_control']);
    $telefono = $conn->real_escape_string($_POST['telefono']);

    // Actualizar la tabla usuarios
    $sql = "UPDATE usuarios SET first_name='$first_name', last_name='$last_name' WHERE email='$email'";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['user_first_name'] = $first_name;
        $_SESSION['user_last_name'] = $last_name;
    }

    // Actualizar la tabla detalles_usuarios
    $sql = "SELECT * FROM detalles_usuarios WHERE usuario_id = '$user_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $sql = "UPDATE detalles_usuarios SET numero_control='$numero_control', telefono='$telefono' WHERE usuario_id='$user_id'";
    } else {
        $sql = "INSERT INTO detalles_usuarios (usuario_id, numero_control, telefono) VALUES ('$user_id', '$numero_control', '$telefono')";
    }
    if ($conn->query($sql) === TRUE) {
        $_SESSION['numero_control'] = $numero_control;
        $_SESSION['telefono'] = $telefono;
        $message = "Datos actualizados exitosamente";
    } else {
        $message = "Error al actualizar los datos: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Its zapopan</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <br />
        <h2>Bienvenido</h2>
        <br />
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">Información del Usuario</div>
                    <div class="card-body">
                        <?php
                        echo '<h5><b>Nombre :</b> ' . $_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name'] . '</h5>';
                        echo '<h5><b>Email :</b> ' . $_SESSION['user_email_address'] . '</h5>';
                        echo '<h5><b>Numero de control o nomina :</b> ' . $_SESSION['numero_control'] . '</h5>';
                        echo '<h5><b>Numero de telefono :</b> ' . $_SESSION['telefono'] . '</h5>';
                        ?>
                        <h6><a href="logout.php">Cerrar Sesión</a></h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">Actualizar Datos</div>
                    <div class="card-body">
                        <?php
                        if (isset($message)) {
                            echo '<div class="alert alert-info">' . $message . '</div>';
                        }
                        ?>
                        <form method="post">
                            <div class="form-group">
                                <label for="first_name">Nombre:</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $_SESSION['user_first_name']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Apellido:</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $_SESSION['user_last_name']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="numero_control">Número de Control:</label>
                                <input type="text" class="form-control" id="numero_control" name="numero_control" value="<?php echo $_SESSION['numero_control']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono">Teléfono:</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $_SESSION['telefono']; ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
