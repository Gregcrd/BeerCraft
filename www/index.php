<?php
session_start();
require 'db.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white flex flex-col items-center py-10">

    <h1 class="text-3xl font-bold mb-6">Bienvenue sur Beercraft</h1>

    <?php if (isset($_SESSION['message'])) : ?>
        <p class="text-green-400 text-center"><?= $_SESSION['message']; ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="mb-6">
        <?php if (isset($_SESSION['user'])) : ?>
            <p class="text-gray-300">Connecté en tant que <strong><?= htmlspecialchars($_SESSION['user']['first_name']) . " " . htmlspecialchars($_SESSION['user']['last_name']) ?></strong></p>
            <p class="text-gray-400">Rôle : <?= htmlspecialchars($_SESSION['user']['role']) ?></p>

            <div class="flex justify-center space-x-4 mt-4">
                <a href="logout.php" class="px-3 py-1 bg-red-500 text-white text-sm rounded-md hover:bg-red-600">
                    Se déconnecter
                </a>
                <a href="beers.php" class="px-3 py-1 bg-yellow-500 text-white text-sm rounded-md hover:bg-yellow-600">
                    Voir les bières
                </a>
            </div>

        <?php else : ?>
            <div class="flex justify-center space-x-4 mt-4">
                <a href="register.php" class="px-3 py-1 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600">
                    S'inscrire
                </a>

                <a href="login.php" class="px-3 py-1 bg-green-500 text-white text-sm rounded-md hover:bg-green-600">
                    Se connecter
                </a>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>