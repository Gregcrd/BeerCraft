<?php
require_once 'db.php';

// Vérifier si un ID est passé en paramètre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de bière manquant.");
}

$id = $_GET['id'];

// Supprimer la bière
$stmt = $pdo->prepare("DELETE FROM Beer WHERE id = ?");
$stmt->execute([$id]);

header("Location: beers.php");
exit;
