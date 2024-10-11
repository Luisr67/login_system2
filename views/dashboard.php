<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #1e88e5;
            margin-bottom: 20px;
        }
        h3 {
            color: #333;
            margin-top: 20px;
        }
        .btn-container {
            margin-top: 20px;
        }
        .btn-container a {
            display: inline-block;
            background-color: #e3f2fd;
            color: #1e88e5;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            margin: 10px;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-container a:hover {
            background-color: #b3e5fc;
            transform: scale(1.05);
        }
        .btn-container button {
            background-color: #4CAF50;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            margin: 10px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-container button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }
        footer {
            text-align: center;
            margin-top: 30px;
            padding: 10px;
            background-color: #1e88e5;
            color: white;
            border-radius: 5px;
        }
        footer a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bienvenido al Panel de Usuario</h2>
        <h3>Materias disponibles</h3>

        <div class="btn-container">
            <a href="proximamente.php">Materia 1</a>
            <a href="proximamente.php">Materia 2</a>
            <a href="proximamente.php">Materia 3</a>
            <a href="proximamente.php">Materia 4</a>
        </div>

        <div class="btn-container">
            <form action="logout.php" method="post">
                <button type="submit">Volver al Inicio</button>
            </form>
        </div>

    </div>
</body>
</html>
