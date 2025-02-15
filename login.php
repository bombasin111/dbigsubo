<?php
session_start();
include 'db.php';

// Verificar si el formulario se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si las claves existen en $_POST
    $usuario = $_POST['usuario'] ?? null;
    $contraseña = $_POST['contraseña'] ?? null;

    // Validar que los campos no estén vacíos
    if (empty($usuario) || empty($contraseña)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        try {
            // Buscar el usuario en la base de datos
            $stmt = $conn->prepare("SELECT * FROM usuarios_iglesia WHERE usuario = :usuario");
            $stmt->execute(['usuario' => $usuario]);

            if ($stmt->rowCount() > 0) {
                $usuario_iglesia = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verificar la contraseña
                if (password_verify($contraseña, $usuario_iglesia['contraseña'])) {
                    // Inicio de sesión exitoso
                    $_SESSION['iglesia_id'] = $usuario_iglesia['iglesia_id'];
                    header("Location: iglesia.php");
                    exit();
                } else {
                    // Contraseña incorrecta
                    $error = "Contraseña incorrecta.";
                }
            } else {
                // Usuario no encontrado
                $error = "Usuario no encontrado.";
            }
        } catch (PDOException $e) {
            $error = "Error en la consulta: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <h1>Login</h1>
    <form method="POST">
        <label for="usuario">Usuario:</label>
        <input type="text" id="usuario" name="usuario" required>
        <br><br>
        <label for="contraseña">Contraseña:</label>
        <input type="password" id="contraseña" name="contraseña" required>
        <br><br>
        <button type="submit">Iniciar Sesión</button>
    </form>
</body>
</html>