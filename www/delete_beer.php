<?php
require 'db.php';
if (!isAdmin()) {
    die("Accès refusé. Vous n'êtes pas administrateur.");
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de bière manquant.");
}

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM Beer WHERE id = ?");
$stmt->execute([$id]);

header("Location: beers.php");
exit;
