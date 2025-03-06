<?php
require 'db.php';
require_once 'header.php';

if (!isAdmin()) {
    die("Accès refusé. Vous n'êtes pas administrateur.");
}

$message = ""; // Initialisation du message

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);
    $stmt = $pdo->prepare("UPDATE User SET role = 'admin' WHERE id = ?");
    $stmt->execute([$userId]);

    $message = "Utilisateur promu administrateur avec succès !";
}

// Récupérer la liste des utilisateurs non-admins
$users = $pdo->query("SELECT * FROM User WHERE role = 'member'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">

    <div class="flex flex-grow items-center justify-center mt-20">
        <div class="bg-white p-8 rounded-lg shadow-md w-96 text-center">
            <h1 class="text-2xl font-bold text-gray-700 mb-6">Gestion des utilisateurs</h1>

            <form method="post" class="space-y-4">
                <label class="block text-gray-600">Promouvoir un utilisateur :</label>
                <select name="user_id" class="w-full px-4 py-2 bg-gray-200 text-black border border-gray-400 rounded-md">
                    <?php foreach ($users as $user) : ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="w-full px-4 py-2 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200">
                    Promouvoir en admin
                </button>
            </form>

            <!-- Affichage du message sous la carte -->
            <?php if (!empty($message)) : ?>
                <p class="mt-4 text-font-semibold"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <div class="mt-6">
                <a href="index.php" class="px-4 py-2 text-gray-700 border border-gray-400 rounded-md hover:bg-gray-200">
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>

</body>

</html>