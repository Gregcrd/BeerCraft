<?php
require_once 'db.php';

// Récupérer les catégories existantes
$stmt = $pdo->query("SELECT * FROM Category ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $origin = trim($_POST['origin']);
    $alcohol = floatval($_POST['alcohol']);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']); // Récupération de l'URL de l'image
    $selectedCategories = $_POST['categories'] ?? [];

    if (!empty($name) && !empty($origin) && $alcohol > 0 && !empty($image)) {
        try {
            $pdo->beginTransaction();

            // Insérer la bière avec l'image
            $stmt = $pdo->prepare("INSERT INTO Beer (name, origin, alcohol, description, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $origin, $alcohol, $description, $image]);
            $beerId = $pdo->lastInsertId();

            // Insérer les catégories sélectionnées
            foreach ($selectedCategories as $categoryId) {
                $stmt = $pdo->prepare("INSERT INTO Beer_Category (beer_id, category_id) VALUES (?, ?)");
                $stmt->execute([$beerId, $categoryId]);
            }

            $pdo->commit();

            header("Location: beers.php");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Erreur : " . $e->getMessage();
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Bière</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-black flex items-center justify-center h-screen">

    <div class="w-96 bg-gray-400 p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-center text-gray-700">Ajouter une Bière</h1>

        <?php if (!empty($message)) : ?>
            <p class="text-red-500 text-center mt-2"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post" class="mt-4 space-y-4">
            <input type="text" name="name" placeholder="Nom" required
                class="w-full px-4 py-2 bg-gray-300 text-black border border-gray-400 rounded-md">

            <input type="text" name="origin" placeholder="Origine" required
                class="w-full px-4 py-2 bg-gray-300 text-black border border-gray-400 rounded-md">

            <input type="number" step="0.1" name="alcohol" placeholder="Taux d'alcool (%)" required
                class="w-full px-4 py-2 bg-gray-300 text-black border border-gray-400 rounded-md">

            <input type="text" name="image" placeholder="URL de l'image" required
                class="w-full px-4 py-2 bg-gray-300 text-black border border-gray-400 rounded-md">

            <textarea name="description" placeholder="Description de la bière" rows="4"
                class="w-full px-4 py-2 bg-gray-300 text-black border border-gray-400 rounded-md"></textarea>

            <label class="block text-gray-700 font-semibold">Catégories :</label>
            <select name="categories[]" multiple class="w-full px-4 py-2 bg-gray-300 text-black border border-gray-400 rounded-md">
                <?php foreach ($categories as $category) : ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <div class="flex justify-between">
                <a href="beers.php" class="bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-500">Annuler</a>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                    Ajouter
                </button>
            </div>
        </form>
    </div>

</body>

</html>