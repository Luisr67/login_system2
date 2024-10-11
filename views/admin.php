<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

require_once '../config/db.php';

$mensaje = '';

$usuarios_bloqueados_query = "SELECT id, username, email FROM users WHERE locked = TRUE";
$usuarios_query = "SELECT id, username, email FROM users WHERE email LIKE ?";
$logs_query = "SELECT u.username, l.access_time 
               FROM access_logs l 
               JOIN users u ON l.user_id = u.id 
               ORDER BY l.access_time DESC"; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['desbloquear'])) {
        $user_id = $_POST['desbloquear'];
        
        $stmt = $pdo->prepare("UPDATE users SET locked = FALSE, intentosfallidos = 0 WHERE id = ?");
        $stmt->execute([$user_id]);
        $mensaje = "Usuario desbloqueado exitosamente.";
    }
}

$blocked_users_result = $pdo->query($usuarios_bloqueados_query)->fetchAll(PDO::FETCH_ASSOC);

$search_results = [];

if (isset($_POST['buscar_email']) && !empty($_POST['buscar_email'])) {
    $search_email = '%' . $_POST['buscar_email'] . '%';
    $stmt = $pdo->prepare($usuarios_query);
    $stmt->execute([$search_email]);
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$logs_result = $pdo->query($logs_query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Administrador</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Dashboard del Administrador</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="mensaje <?php echo strpos($mensaje, 'exitosamente') !== false ? 'exito' : 'error'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <h3 style="text-decoration: underline;">Buscar Usuarios por Email</h3>
        <form method="post" action="admin.php">
            <label for="buscar_email">Correo Electrónico:</label>
            <input type="text" id="buscar_email" name="buscar_email" value="<?php echo htmlspecialchars(isset($_POST['buscar_email']) ? $_POST['buscar_email'] : ''); ?>">
            <button type="submit">Buscar</button>
        </form>

        <?php if (isset($_POST['buscar_email']) && !empty($_POST['buscar_email'])): ?>
            <h3 style="text-decoration: underline;">Resultados de la Búsqueda</h3>
            <table style="border: 1px solid black; border-collapse: collapse;">
                <tr>
                    <th style="border: 1px solid black;">ID</th>
                    <th style="border: 1px solid black;">Nombre de Usuario</th>
                    <th style="border: 1px solid black;">Correo Electrónico</th>
                </tr>
                <?php foreach ($search_results as $row): ?>
                <tr>
                    <td style="border: 1px solid black;"><?php echo htmlspecialchars($row['id']); ?></td>
                    <td style="border: 1px solid black;"><?php echo htmlspecialchars($row['username']); ?></td>
                    <td style="border: 1px solid black;"><?php echo htmlspecialchars($row['email']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>

        <?php endif; ?>

        <h3 style="text-decoration: underline;">Usuarios Bloqueados</h3>
        <table style="border: 1px solid black; border-collapse: collapse;">
            <tr>
                <th style="border: 1px solid black;">ID</th>
                <th style="border: 1px solid black;">Nombre de Usuario</th>
                <th style="border: 1px solid black;">Correo Electrónico</th>
                <th style="border: 1px solid black;">Acción</th>
            </tr>
            <?php foreach ($blocked_users_result as $row): ?>
            <tr>
                <td style="border: 1px solid black;"><?php echo htmlspecialchars($row['id']); ?></td>
                <td style="border: 1px solid black;"><?php echo htmlspecialchars($row['username']); ?></td>
                <td style="border: 1px solid black;"><?php echo htmlspecialchars($row['email']); ?></td>
                <td style="border: 1px solid black;">
                <form method="post" action="admin.php">
                    <input type="hidden" name="desbloquear" value="<?php echo htmlspecialchars($row['id']); ?>">
                    <button type="submit">Desbloquear</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>


        <h3 style="text-decoration: underline;">Logs de Accesos</h3>
        <table style="border: 1px solid black; border-collapse: collapse;">
            <tr>
                <th style="border: 1px solid black;">Nombre de Usuario</th>
                <th style="border: 1px solid black;">Fecha y Hora de Acceso</th>
            </tr>
            <?php foreach ($logs_result as $log): ?>
            <tr>
                <td style="border: 1px solid black;"><?php echo htmlspecialchars($log['username']); ?></td>
                <td style="border: 1px solid black;"><?php echo htmlspecialchars($log['access_time']); ?></td>
            </tr>
        <?php endforeach; ?>
        </table>

        <div class="btn-container">
            <form action="logout.php" method="post">
                <button type="submit">Volver al Inicio</button>
            </form>
        </div>
    </div>
</body>
</html>
