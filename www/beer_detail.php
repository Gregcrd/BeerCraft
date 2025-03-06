<?php
require 'db.php';
require_once 'header.php';

// Vérifier si l'ID est présent dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de bière manquant.");
}

$id = $_GET['id'];

// Récupérer les infos de la bière
$stmt = $pdo->prepare("SELECT * FROM Beer WHERE id = ?");
$stmt->execute([$id]);
$beer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$beer) {
    die("Bière non trouvée.");
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de la Bière</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">

    <div class="flex flex-grow items-center justify-center mt-20">
        <div class="bg-white p-8 rounded-lg shadow-md w-96 text-center">
            <h1 class="text-2xl font-bold text-gray-700 mb-4"><?= htmlspecialchars($beer['name']) ?></h1>

            <?php if (!empty($beer['image'])) : ?>
                <img src="<?= htmlspecialchars($beer['image']) ?>" alt="Image de <?= htmlspecialchars($beer['name']) ?>" class="w-full h-40 object-cover rounded-md mb-4">
            <?php endif; ?>

            <p class="text-gray-600">Origine : <?= htmlspecialchars($beer['origin']) ?></p>
            <p class="text-gray-600">Taux d'alcool : <?= htmlspecialchars($beer['alcohol']) ?>%</p>

            <?php if (!empty($beer['description'])) : ?>
                <p class="text-gray-500 mt-2"><?= nl2br(htmlspecialchars($beer['description'])) ?></p>
            <?php endif; ?>

            <div class="mt-6 flex justify-center space-x-4">
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') : ?>
                    <a href="edit_beer.php?id=<?= $beer['id'] ?>" class="px-4 py-2 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200">
                        Modifier
                    </a>
                    <a href="delete_beer.php?id=<?= $beer['id'] ?>" class="px-4 py-2 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200" onclick="return confirm('Supprimer cette bière ?')">
                        Supprimer
                    </a>
                <?php endif; ?>
                <a href="beers.php" class="px-4 py-2 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200">
                    Retour
                </a>
            </div>
        </div>
    </div>

</body>

</html>