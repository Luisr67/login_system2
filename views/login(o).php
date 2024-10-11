<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificamos si el usuario ya está logueado
if (isset($_SESSION['user_id'])) {
    echo "LOGGED IN !";
    // Redirigimos según el rol del usuario
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
}

require_once '../includes/headers.php';

// Conexión a la base de datos (ajusta con tu configuración)
$conn = new mysqli('localhost', 'tu_usuario', 'tu_password', 'tu_base_de_datos');

if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Obtenemos los datos del formulario de login
$username = $_POST['username'];
$password = $_POST['password'];

// Preparamos la consulta para verificar las credenciales del usuario
$stmt = $conn->prepare("SELECT id, role FROM usuarios WHERE username = ? AND password = ?");
$stmt->bind_param('ss', $username, $password);
$stmt->execute();
$stmt->store_result();

// Si las credenciales son correctas, obtenemos el id y role del usuario
if ($stmt->num_rows > 0) {
    $stmt->bind_result($user_id, $role);
    $stmt->fetch();

    // Guardamos el id y role del usuario en la sesión
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = $role;

    // Debugging (opcional): imprime el ID y el rol
    // echo "User ID: " . $_SESSION['user_id'] . "<br>";
    // echo "Role: " . $_SESSION['role'] . "<br>";
    // exit();

    // Redirigimos al dashboard correspondiente
    if ($role === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
} else {
    echo "Credenciales incorrectas.";
}

$stmt->close();
$conn->close();
?>
