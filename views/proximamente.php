<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Próximamente</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 600px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #1e88e5;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            color: #666;
        }
        .btn-container {
            margin-top: 20px;
        }
        .btn-container button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
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
        <h2>Página en desarrollo</h2>

        <div class="btn-container">
            <button onclick="window.location.href = 'dashboard.php';">Volver al Panel de Usuario</button>
        </div>
    </div>
</body>
</html>
