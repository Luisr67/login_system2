<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token'];
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function authenticate_user($username, $password, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

function register_user($username, $email, $password, $role, $pdo) {
    if (user_exists($username, $email, $pdo)) {
        return false;
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
    return $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashed_password, 'role' => $role]);
}

function user_exists($username, $email, $pdo) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
    $stmt->execute(['username' => $username, 'email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function log_access($user_id, $pdo) {
    $stmt = $pdo->prepare("INSERT INTO access_logs (user_id, access_time) VALUES (:user_id, NOW())");
    $stmt->execute(['user_id' => $user_id]);
}

function is_valid_password($password) {
    // Verificar que la contraseña cumpla con los requisitos
    if (strlen($password) < 8) {
        return false; // Al menos 8 caracteres
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return false; // Al menos una letra mayúscula
    }
    if (!preg_match('/[a-z]/', $password)) {
        return false; // Al menos una letra minúscula
    }
    if (!preg_match('/[0-9]/', $password)) {
        return false; // Al menos un número
    }
    if (!preg_match('/[\W_]/', $password)) {
        return false; // Al menos un símbolo
    }
    return true; // La contraseña es válida
}

function get_logs($pdo) {
    $stmt = $pdo->query("SELECT users.username, access_logs.access_time FROM access_logs INNER JOIN users ON access_logs.user_id = users.id ORDER BY access_logs.access_time DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function is_admin($user_id, $pdo) {
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user && $user['role'] === 'admin';
}

function send_password_reset($email, $pdo) {
    $user = user_exists_by_email($email, $pdo);
    if ($user) {
        $reset_token = bin2hex(random_bytes(16));
        $stmt = $pdo->prepare("UPDATE users SET reset_token = :reset_token WHERE email = :email");
        if ($stmt->execute(['reset_token' => $reset_token, 'email' => $email])) {
            // Aquí deberías enviar un correo electrónico con el enlace de restablecimiento de contraseña
            return true;
        }
    }
    return false;
}

function user_exists_by_email($email, $pdo) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function unlock_account($user_id, $pdo) {
    $stmt = $pdo->prepare("UPDATE users SET locked = 0 WHERE id = :id");
    return $stmt->execute(['id' => $user_id]);
}
?>
