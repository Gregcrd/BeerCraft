<?php
ob_start();
session_start();
require 'db.php';
require_once 'header.php'; // Inclusion du header
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            header("Location: index.php");
            exit;
        } else {
            $message = "Email ou mot de passe incorrect.";
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
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">

    <div class="flex flex-grow items-center justify-center mt-20">
        <div class="bg-white p-8 rounded-lg shadow-md w-96 text-center">
            <h1 class="text-2xl font-bold text-gray-700 mb-6">Connexion</h1>

            <?php if (!empty($message)) : ?>
                <p class="text-red-500 text-center"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="post" class="space-y-4">
                <input type="email" name="email" placeholder="Email" required
                    class="w-full px-4 py-2 bg-gray-200 text-black border border-gray-400 rounded-md">

                <input type="password" name="password" placeholder="Mot de passe" required
                    class="w-full px-4 py-2 bg-gray-200 text-black border border-gray-400 rounded-md">

                <button type="submit" class="w-full px-4 py-2 bg-gray-300 text-gray-700 rounded-md border border-gray-400 hover:bg-gray-400">
                    Se connecter
                </button>
            </form>

            <p class="text-center text-gray-600 mt-4">
                Pas encore de compte ? <a href="register.php" class="text-blue-500 hover:underline">S'inscrire</a>
            </p>
        </div>
    </div>

</body>

</html>