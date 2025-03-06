<?php
session_start();
require 'db.php';
require_once 'header.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">

    <!-- Conteneur principal pour centrer la carte -->
    <div class="flex flex-grow items-center justify-center mt-20">
        <div class="bg-white p-8 rounded-lg shadow-md w-96 text-center">
            <h1 class="text-2xl font-bold text-gray-700 mb-4">Bienvenue sur Beercraft !</h1>

            <?php if (isset($_SESSION['user'])) : ?>
                <p class="text-gray-600 mb-4">Connecté en tant que <strong><?= htmlspecialchars($_SESSION['user']['first_name']) ?></strong></p>
                <p class="text-gray-500 mb-6">Rôle : <?= htmlspecialchars($_SESSION['user']['role']) ?></p>
                <a href="beers.php" class="px-4 py-2 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200">
                    Voir les bières
                </a>
                <a href="logout.php" class="px-4 py-2 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200 ml-2">
                    Déconnexion
                </a>
            <?php else : ?>
                <a href="register.php" class="px-4 py-2 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200">
                    S'inscrire
                </a>
                <a href="login.php" class="px-4 py-2 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200 ml-2">
                    Se connecter
                </a>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>