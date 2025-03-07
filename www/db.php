<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "mysql-db";
$dbname = "BeerCraft2"; // Nom de la base de données
$username = "root";
$password = "root";

// Vérification pour éviter de redéclarer la fonction
if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
