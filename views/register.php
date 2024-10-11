<?php
require_once '../config/db.php';
require_once '../includes/headers.php';
require_once '../includes/functions.php';

$success_message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $csrf_token = $_POST['csrf_token'];

    if (!validate_csrf_token($csrf_token)) {
        die("Token CSRF inválido.");
    }

    if (strlen($password) >= 8 && preg_match("/[A-Z]/", $password) && preg_match("/[0-9]/", $password) && preg_match("/[\W]/", $password)) {

        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_user) {
            $error = "El nombre de usuario o el correo electrónico ya están en uso.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password, $role]);

            $success_message = "Registro exitoso. Serás redirigido al inicio de sesión en breve.";
        }
    } else {
        $error = "La contraseña debe tener al menos 8 caracteres, incluyendo una letra mayúscula, una letra minúscula, un número y un símbolo.";
    }
}

$csrf_token = generate_csrf_token(); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <?php if ($success_message): ?>
        <meta http-equiv="refresh" content="2;url=index.php">
    <?php endif; ?>
</head>
<body>
    <div class="container">
        <h1>Registro</h1>
        
        <?php if ($success_message): ?>
            <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="" method="POST"> 
            <input type="text" name="username" placeholder="Nombre de Usuario" required>
            <input type="email" name="email" placeholder="Correo Electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <select name="role" required>
                <option value="user">Usuario</option>
                <option value="admin">Admin</option>
            </select>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="submit" value="Registrarse">
        </form>
        <a href="index.php">Volver al Inicio de Sesión</a>
    </div>
</body>
</html>
