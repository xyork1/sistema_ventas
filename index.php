<?php
session_start();
require_once 'conexionDB.php';

if (isset($_SESSION['rol'])) {
    $rol = $_SESSION['rol'];
    if ($rol === 'vendedor') {
        header('Location: vendedor.php');
        exit();
    } elseif ($rol === 'dueno') {
        header('Location: dueno.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $password = md5($_POST['password']); // Hash de contraseña simple, idealmente usa password_hash

    $query = "SELECT rol FROM usuarios WHERE correo = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $correo, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['rol'] = $row['rol'];
        $_SESSION['correo'] = $correo;

        if ($row['rol'] === 'vendedor') {
            header('Location: vendedor.php');
        } elseif ($row['rol'] === 'dueno') {
            header('Location: dueno.php');
        }
        exit();
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .login-container {
            width: 300px; margin: 100px auto; padding: 20px;
            background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        input, button { width: 100%; padding: 10px; margin: 10px 0; }
        button { background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="index.php">
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
