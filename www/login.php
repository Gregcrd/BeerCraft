<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];

        if (!$email || !$password) {
            throw new Exception("Tous les champs sont obligatoires !");
        }

        // Vérifier si l'utilisateur existe
        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Email ou mot de passe incorrect.");
        }

        // Stocker l'utilisateur en session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        $_SESSION['message'] = "Connexion réussie !";
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white flex items-center justify-center h-screen">

    <div class="w-96 bg-gray-800 p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-center text-red-400">Se connecter</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-500 text-center"><?= $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="post" class="mt-4 space-y-4">
            <input type="email" name="email" placeholder="Email" required
                class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-md">

            <input type="password" name="password" placeholder="Mot de passe" required
                class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-md">

            <div class="flex justify-between">
                <a href="index.php" class="bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-500">Annuler</a>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                    Connexion
                </button>
            </div>
        </form>
    </div>

</body>

</html>