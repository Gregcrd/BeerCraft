<?php
require_once 'db.php';

// Vérifier si un ID est passé en paramètre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de bière manquant.");
}

$id = $_GET['id'];

// Récupérer la bière
$stmt = $pdo->prepare("SELECT * FROM Beer WHERE id = ?");
$stmt->execute([$id]);
$beer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$beer) {
    die("Bière non trouvée.");
}

// Récupérer toutes les catégories existantes
$stmt = $pdo->query("SELECT * FROM Category ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les catégories associées à la bière
$stmt = $pdo->prepare("
    SELECT category_id FROM Beer_Category WHERE beer_id = ?
");
$stmt->execute([$id]);
$selectedCategories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Mise à jour de la bière
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $origin = trim($_POST['origin']);
    $alcohol = floatval($_POST['alcohol']);
    $description = trim($_POST['description']);
    $newCategories = $_POST['categories'] ?? [];

    if (!empty($name) && !empty($origin) && $alcohol > 0) {
        try {
            $pdo->beginTransaction();

            // Mettre à jour les informations de la bière
            $stmt = $pdo->prepare("UPDATE Beer SET name = ?, origin = ?, alcohol = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $origin, $alcohol, $description, $id]);

            // Supprimer les anciennes catégories
            $stmt = $pdo->prepare("DELETE FROM Beer_Category WHERE beer_id = ?");
            $stmt->execute([$id]);

            // Insérer les nouvelles catégories sélectionnées
            foreach ($newCategories as $categoryId) {
                $stmt = $pdo->prepare("INSERT INTO Beer_Category (beer_id, category_id) VALUES (?, ?)");
                $stmt->execute([$id, $categoryId]);
            }

            $pdo->commit();

            header("Location: beers.php");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            die("Erreur : " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Bière</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white flex items-center justify-center h-screen">

    <div class="w-96 bg-gray-800 p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-center text-red-400">Modifier la Bière</h1>

        <form method="post" class="mt-4 space-y-4">
            <input type="text" name="name" value="<?= htmlspecialchars($beer['name']) ?>" required
                class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-md">

            <input type="text" name="origin" value="<?= htmlspecialchars($beer['origin']) ?>" required
                class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-md">

            <input type="number" step="0.1" name="alcohol" value="<?= htmlspecialchars($beer['alcohol']) ?>" required
                class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-md">

            <textarea name="description" rows="4"
                class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-md"><?= htmlspecialchars_decode($beer['description']) ?></textarea>

            <label class="block text-gray-400">Catégories :</label>
            <select name="categories[]" multiple class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-md">
                <?php foreach ($categories as $category) : ?>
                    <option value="<?= $category['id'] ?>" <?= in_array($category['id'], $selectedCategories) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="flex justify-between">
                <a href="beers.php" class="bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-500">Annuler</a>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                    Modifier
                </button>
            </div>
        </form>
    </div>

</body>

</html>