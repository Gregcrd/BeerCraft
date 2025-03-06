<?php
require 'db.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beercraft</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-200 text-black">

    <!-- Barre de navigation -->
    <nav class="bg-white shadow-md px-8 py-4 flex justify-between items-center">
        <a href="index.php" class="text-xl font-bold text-gray-700">Beercraft</a>

        <div class="space-x-4">
            <a href="beers.php" class="text-gray-600 hover:text-gray-800">Bières</a>

            <?php if (isset($_SESSION['user'])) : ?>
                <a href="index.php" class="text-gray-600 hover:text-gray-800">
                    <?= htmlspecialchars($_SESSION['user']['first_name']) ?>
                </a>

                <?php if (isAdmin()) : ?>
                    <a href="admin_dashboard.php" class="text-gray-600 hover:text-gray-800">Admin</a>
                <?php endif; ?>

                <?php if (isAdmin()) : ?>
                    <a href="add_beer.php" class="px-3 py-1 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200">
                        + Ajouter une Bière
                    </a>
                <?php endif; ?>

                <a href="logout.php" class="px-3 py-1 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200">Déconnexion</a>
            <?php else : ?>
                <a href="register.php" class="px-3 py-1 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200">Inscription</a>
                <a href="login.php" class="px-3 py-1 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200">Connexion</a>

            <?php endif; ?>
        </div>
    </nav>