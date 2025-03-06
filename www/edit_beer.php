<?php
require 'db.php';
if (!isAdmin()) {
    die("Accès refusé. Vous n'êtes pas administrateur.");
}


if (!isset($_GET['id'])) {
    header("Location: beers.php");
    exit;
}

$id = $_GET['id'];

// Récupérer la bière
$stmt = $pdo->prepare("SELECT * FROM Beer WHERE id = ?");
$stmt->execute([$id]);
$beer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$beer) {
    header("Location: beers.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $origin = trim($_POST['origin']);
    $alcohol = floatval($_POST['alcohol']);
    $description = trim($_POST['description']);

    // Vérifie si l'utilisateur a ajouté une nouvelle image
    $image = !empty($_POST['image']) ? trim($_POST['image']) : $beer['image'];

    if (!empty($name) && !empty($origin) && $alcohol > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE Beer SET name = ?, origin = ?, alcohol = ?, description = ?, image = ? WHERE id = ?");
            $stmt->execute([$name, $origin, $alcohol, $description, $image, $id]);

            header("Location: beers.php");
            exit;
        } catch (PDOException $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    } else {
        $message = "Veuillez remplir tous les champs obligatoires.";
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

<body class="bg-white text-black flex items-center justify-center h-screen">

    <div class="w-96 bg-gray-400 p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-center text-gray-700">Modifier une Bière</h1>

        <?php if (!empty($message)) : ?>
            <p class="text-red-500 text-center mt-2"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post" class="mt-4 space-y-4">
            <input type="text" name="name" value="<?= htmlspecialchars($beer['name']) ?>" required
                class="w-full px-4 py-2 bg-gray-300 text-black border border-gray-400 rounded-md">

            <input type="text" name="origin" value="<?= htmlspecialchars($beer['origin']) ?>" required
                class="w-full px-4 py-2 bg-gray-300 text-black border border-gray-400 rounded-md">

            <input type="number" step="0.1" name="alcohol" value="<?= htmlspecialchars($beer['alcohol']) ?>" required
                class="w-full px-4 py-2 bg-gray-300 text-black border border-gray-400 rounded-md">

            <textarea name="description" rows="4"
                class="w-full px-4 py-2 bg-gray-300 text-black border border-gray-400 rounded-md"><?= htmlspecialchars($beer['description']) ?></textarea>

            <!-- Afficher l'image actuelle si elle existe -->
            <?php if (!empty($beer['image'])) : ?>
                <p class="text-gray-700 text-sm">Image actuelle :</p>
                <img src="<?= htmlspecialchars($beer['image']) ?>" alt="Image de la bière"
                    class="w-full h-40 object-cover rounded-md">
            <?php else : ?>
                <!-- Champ pour ajouter une image si elle est manquante -->
                <input type="text" name="image" placeholder="URL de l'image"
                    class="w-full px-4 py-2 bg-gray-300 text-black border border-gray-400 rounded-md">
            <?php endif; ?>

            <div class="flex justify-between">
                <a href="beers.php" class="bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-500">Annuler</a>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

</body>

</html>