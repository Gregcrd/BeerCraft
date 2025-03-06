<?php
require 'db.php';
require_once 'header.php';

// Récupérer toutes les catégories  filtre
$categories = $pdo->query("SELECT * FROM Category ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si une catégorie est sélectionnée
$selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : null;

// Récupérer les bières en fonction de la catégorie sélectionnée
if ($selectedCategory) {
    $stmt = $pdo->prepare("
        SELECT b.* FROM Beer b
        INNER JOIN Beer_Category bc ON b.id = bc.beer_id
        WHERE bc.category_id = ?
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$selectedCategory]);
} else {
    // Si aucune catégorie sélectionnée, afficher toutes les bières
    $stmt = $pdo->query("SELECT * FROM Beer ORDER BY created_at DESC");
}
$beers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Bières</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">

    <div class="container mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold text-center text-gray-700 mb-6">Notre catalogue</h1>

        <!-- Formulaire de filtre par catégorie -->
        <div class="mb-6 flex justify-center">
            <form method="GET" class="flex items-center space-x-4">
                <label for="category" class="text-gray-700">Filtrer par catégorie :</label>
                <select name="category" id="category" class="px-4 py-2 bg-gray-200 border border-gray-400 rounded-md">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= $category['id'] ?>" <?= ($selectedCategory == $category['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="px-4 py-2 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200">
                    Appliquer
                </button>
            </form>
        </div>

        <!-- Bouton pour réinitialiser le filtre -->
        <?php if ($selectedCategory) : ?>
            <div class="text-center mb-6">
                <a href="beers.php" class="text-gray-600 hover:underline">Réinitialiser le filtre</a>
            </div>
        <?php endif; ?>

        <!-- Affichage des bières -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php if (empty($beers)) : ?>
                <p class="col-span-full text-center text-gray-400">Aucune bière trouvée.</p>
            <?php else : ?>
                <?php foreach ($beers as $beer) : ?>
                    <div class="bg-white border border-gray-300 p-5 rounded-lg shadow-sm flex flex-col items-center text-center">

                        <!-- Affichage de l'image -->
                        <?php if (!empty($beer['image'])) : ?>
                            <img src="<?= htmlspecialchars($beer['image']) ?>" alt="Image de <?= htmlspecialchars($beer['name']) ?>"
                                class="w-full h-40 object-cover rounded-md mb-4">
                        <?php endif; ?>

                        <h2 class="text-lg font-semibold"><?= htmlspecialchars($beer['name']) ?></h2>
                        <p class="text-sm text-gray-600">Origine : <?= htmlspecialchars($beer['origin']) ?></p>
                        <p class="text-sm text-gray-600">Taux d'alcool : <?= htmlspecialchars($beer['alcohol']) ?>%</p>

                        <!-- Affichage des catégories associées -->
                        <?php
                        $stmt = $pdo->prepare("
                            SELECT c.name FROM Category c
                            INNER JOIN Beer_Category bc ON c.id = bc.category_id
                            WHERE bc.beer_id = ?
                        ");
                        $stmt->execute([$beer['id']]);
                        $beerCategories = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        ?>

                        <?php if (!empty($beerCategories)) : ?>
                            <p class="text-sm text-gray-600 mt-2">Catégories : <?= implode(', ', $beerCategories) ?></p>
                        <?php endif; ?>

                        <!-- Bouton "En savoir plus" -->
                        <div class="mt-4">
                            <a href="beer_detail.php?id=<?= $beer['id'] ?>"
                                class="px-4 py-2 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200">
                                En savoir plus
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>