<?php
session_start();
require 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $role = "member"; // Par défaut, tous les utilisateurs sont "member"

    if (!empty($firstName) && !empty($lastName) && $email && !empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO User (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $role]);

            $_SESSION['message'] = "Inscription réussie ! Vous pouvez vous connecter.";
            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    } else {
        $message = "Veuillez remplir tous les champs correctement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h1 class="text-2xl font-bold text-center text-gray-700 mb-6">Inscription</h1>

        <?php if (!empty($message)) : ?>
            <p class="text-red-500 text-center"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <input type="text" name="first_name" placeholder="Prénom" required
                class="w-full px-4 py-2 bg-gray-200 text-black border border-gray-400 rounded-md">

            <input type="text" name="last_name" placeholder="Nom" required
                class="w-full px-4 py-2 bg-gray-200 text-black border border-gray-400 rounded-md">

            <input type="email" name="email" placeholder="Email" required
                class="w-full px-4 py-2 bg-gray-200 text-black border border-gray-400 rounded-md">

            <input type="password" name="password" placeholder="Mot de passe" required
                class="w-full px-4 py-2 bg-gray-200 text-black border border-gray-400 rounded-md">

            <button type="submit" class="w-full px-4 py-2 bg-gray-300 text-gray-700 rounded-md border border-gray-400 hover:bg-gray-400">
                S'inscrire
            </button>
        </form>

        <p class="text-center text-gray-600 mt-4">
            Déjà un compte ? <a href="login.php" class="text-blue-500 hover:underline">Se connecter</a>
        </p>
    </div>

</body>

</html>