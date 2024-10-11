<?php
require_once '../includes/headers.php';
require_once '../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
        exit();
    } else {
        header('Location: dashboard.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, role, password, locked, intentosfallidos FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['locked']) {
            $error = "La cuenta está bloqueada. Contacta al administrador.";
        } else {
            if (password_verify($password, $user['password'])) {
                $stmt = $pdo->prepare("UPDATE users SET intentosfallidos = 0 WHERE id = ?");
                $stmt->execute([$user['id']]);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                $stmt = $pdo->prepare("INSERT INTO access_logs (user_id, access_time) VALUES (?, NOW())");
                $stmt->execute([$user['id']]);

                if ($user['role'] === 'admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit();
            } else {
                $stmt = $pdo->prepare("UPDATE users SET intentosfallidos = intentosfallidos + 1 WHERE id = ?");
                $stmt->execute([$user['id']]);

                if ($user['intentosfallidos'] + 1 >= 3) {
                    $stmt = $pdo->prepare("UPDATE users SET locked = TRUE WHERE id = ?");
                    $stmt->execute([$user['id']]);
                    $error = "Se han superado los intentos, contacte al administrador.";
                } else {
                    $error = "Credenciales incorrectas. Intentos restantes: " . (3 - ($user['intentosfallidos'] + 1));
                }
            }
        }
    } else {
        $error = "Credenciales incorrectas.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h5>Sistema Login Grupo B4</h5>
        <h1>Inicio de sesión</h1>
        <form action="index.php" method="POST">
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <input type="submit" value="Acceder">
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
        </form>
        <a href="register.php">Registrarse</a> | <a href="forgot_password.php">¿Olvidaste tu contraseña?</a>
    </div>
</body>
</html>
