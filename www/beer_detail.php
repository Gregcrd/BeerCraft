<?php
ob_start();
require_once 'db.php';
require_once 'header.php';

// Vérifier si l'ID de la bière est présent dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de bière manquant.");
}

$beer_id = $_GET['id'];

// Récupérer les détails de la bière
$stmt = $pdo->prepare("SELECT * FROM Beer WHERE id = ?");
$stmt->execute([$beer_id]);
$beer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$beer) {
    die("Bière non trouvée.");
}

// Récupérer les catégories associées
$stmt = $pdo->prepare("
    SELECT c.name FROM Category c
    INNER JOIN Beer_Category bc ON c.id = bc.category_id
    WHERE bc.beer_id = ?
");
$stmt->execute([$beer_id]);
$beerCategories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Récupérer les commentaires pour cette bière
$stmt = $pdo->prepare("
    SELECT c.content, c.rating, c.created_at, u.first_name, u.last_name
    FROM Comment c
    JOIN User u ON c.user_id = u.id
    WHERE c.beer_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$beer_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gérer l'ajout d'un commentaire
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_comment'])) {
    if (!isset($_SESSION['user'])) {
        die("Vous devez être connecté pour commenter.");
    }

    $content = trim($_POST['content']);
    $rating = intval($_POST['rating']);
    $user_id = $_SESSION['user']['id'];

    if (!empty($content) && $rating >= 1 && $rating <= 5) {
        $stmt = $pdo->prepare("INSERT INTO Comment (content, rating, user_id, beer_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$content, $rating, $user_id, $beer_id]);
        header("Location: beer_detail.php?id=$beer_id"); // Rafraîchir la page
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Bière</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-black flex flex-col items-center py-10">

    <!-- Conteneur principal avec espace sous le header -->
    <div class="w-full max-w-3xl bg-white p-8 rounded-lg shadow-md mx-auto mt-20">

        <!-- Titre -->
        <h1 class="text-3xl font-bold text-center text-gray-700"><?= htmlspecialchars($beer['name']) ?></h1>

        <!-- Image de la bière -->
        <?php if (!empty($beer['image'])) : ?>
            <img src="<?= htmlspecialchars($beer['image']) ?>" alt="Image de <?= htmlspecialchars($beer['name']) ?>"
                class="w-full h-60 object-cover rounded-md mt-4">
        <?php endif; ?>

        <div class="mt-4 text-gray-600 text-lg">
            <p><strong>Origine :</strong> <?= htmlspecialchars($beer['origin']) ?></p>
            <p><strong>Taux d'alcool :</strong> <?= htmlspecialchars($beer['alcohol']) ?>%</p>

            <?php if (!empty($beer['description'])) : ?>
                <p class="mt-2"><?= htmlspecialchars($beer['description']) ?></p>
            <?php endif; ?>

            <?php if (!empty($beerCategories)) : ?>
                <p class="mt-2"><strong>Catégories :</strong> <?= implode(', ', $beerCategories) ?></p>
            <?php endif; ?>
        </div>

        <!-- Boutons d'action -->
        <div class="flex justify-center space-x-4 mt-6">
            <a href="beers.php" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md border border-gray-400">Retour</a>

            <?php if (isset($_SESSION['user']) && isAdmin()) : ?>
                <a href="edit_beer.php?id=<?= $beer['id'] ?>" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md border border-gray-400">
                    Modifier
                </a>
                <a href="delete_beer.php?id=<?= $beer['id'] ?>"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md border border-gray-400"
                    onclick="return confirm('Supprimer cette bière ?')">
                    Supprimer
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Section Commentaires -->
    <div class="w-full max-w-3xl bg-white p-8 rounded-lg shadow-md mt-6 mx-auto">
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Commentaires</h2>

        <?php if (empty($comments)) : ?>
            <p class="text-gray-600">Aucun commentaire pour cette bière.</p>
        <?php else : ?>
            <div class="space-y-4">
                <?php foreach ($comments as $comment) : ?>
                    <div class="bg-gray-100 p-4 rounded-md flex justify-between items-center">
                        <div>
                            <p class="font-bold"><?= htmlspecialchars($comment['first_name'] . " " . $comment['last_name']) ?></p>
                            <p class="text-gray-700"><?= htmlspecialchars($comment['content']) ?></p>
                            <small class="text-gray-500"><?= $comment['created_at'] ?></small>
                        </div>
                        <div class="text-yellow-500 text-lg font-bold">
                            <?= str_repeat('⭐', $comment['rating']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Formulaire d'ajout de commentaire -->
    <?php if (isset($_SESSION['user'])) : ?>
        <div class="w-full max-w-3xl bg-white p-8 rounded-lg shadow-md mt-6 mx-auto">
            <h2 class="text-lg font-bold text-gray-700 mb-4">Laisser un commentaire</h2>
            <form method="post" class="space-y-4">
                <textarea name="content" placeholder="Votre commentaire" required
                    class="w-full p-4 bg-gray-200 text-black border border-gray-400 rounded-md resize-none h-24"></textarea>

                <select name="rating" class="w-full p-2 bg-gray-200 text-black border border-gray-400 rounded-md">
                    <option value="5">⭐️⭐️⭐️⭐️⭐️</option>
                    <option value="4">⭐️⭐️⭐️⭐️</option>
                    <option value="3">⭐️⭐️⭐️</option>
                    <option value="2">⭐️⭐️</option>
                    <option value="1">⭐️</option>
                </select>

                <button type="submit" name="submit_comment"
                    class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md border border-gray-400">
                    Publier
                </button>
            </form>
        </div>
    <?php else : ?>
        <p class="text-gray-600 mt-4 text-center">Connectez-vous pour laisser un commentaire.</p>
    <?php endif; ?>

</body>

</html>