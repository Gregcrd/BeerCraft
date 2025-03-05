<?php
require_once 'db.php';

// Récupérer les bières
$stmt = $pdo->query("SELECT * FROM Beer ORDER BY created_at DESC");
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

<body class="bg-gray-900 text-white flex flex-col items-center py-10">

    <h1 class="text-3xl font-bold mb-6">Liste des Bières</h1>

    <a href="add_beer.php" class="mb-6 px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
        + Ajouter une Bière
    </a>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-11/12">
        <?php if (empty($beers)) : ?>
            <p class="col-span-full text-center text-gray-400">Aucune bière trouvée.</p>
        <?php else : ?>
            <?php foreach ($beers as $beer) : ?>
                <div class="bg-gray-800 p-5 rounded-lg shadow-md flex flex-col justify-between h-60">
                    <div>
                        <h2 class="text-xl font-bold text-red-400"><?= htmlspecialchars($beer['name']) ?></h2>
                        <p class="text-gray-300">Origine : <?= htmlspecialchars($beer['origin']) ?></p>
                        <p class="text-gray-300">Taux d'alcool : <?= htmlspecialchars($beer['alcohol']) ?>%</p>

                        <?php if (!empty($beer['description'])) : ?>
                            <p class="text-gray-400 mt-2 truncate"><?= htmlspecialchars($beer['description']) ?></p>
                        <?php endif; ?>

                        <?php
                        // Récupérer les catégories associées
                        $stmt = $pdo->prepare("
                            SELECT c.name FROM Category c
                            INNER JOIN Beer_Category bc ON c.id = bc.category_id
                            WHERE bc.beer_id = ?
                        ");
                        $stmt->execute([$beer['id']]);
                        $beerCategories = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        ?>

                        <?php if (!empty($beerCategories)) : ?>
                            <p class="text-gray-400 mt-2">Catégories : <?= implode(', ', $beerCategories) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4 flex justify-between">
                        <a href="edit_beer.php?id=<?= $beer['id'] ?>"
                            class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600">
                            Modifier
                        </a>
                        <a href="delete_beer.php?id=<?= $beer['id'] ?>"
                            class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600"
                            onclick="return confirm('Supprimer cette bière ?')">
                            Supprimer
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>

</html>