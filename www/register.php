<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db.php';

    try {
        $firstName = trim(htmlspecialchars($_POST['first-name'], ENT_QUOTES, 'UTF-8'));
        $lastName = trim(htmlspecialchars($_POST['last-name'], ENT_QUOTES, 'UTF-8'));
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];
        $role = trim(htmlspecialchars($_POST['role'], ENT_QUOTES, 'UTF-8'));

        if (!$firstName || !$lastName || !$email || !$password || !$role) {
            throw new Exception("Tous les champs sont obligatoires !");
        }

        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM User WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Cet email est déjà utilisé !");
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO User (first_name, last_name, email, password, role) 
                               VALUES (:first_name, :last_name, :email, :password, :role)");

        $stmt->bindValue(':first_name', $firstName, PDO::PARAM_STR);
        $stmt->bindValue(':last_name', $lastName, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindValue(':role', $role, PDO::PARAM_STR);

        $stmt->execute();

        // Stocker l'utilisateur en session
        $_SESSION['user'] = [
            'id' => $pdo->lastInsertId(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'role' => $role
        ];

        $_SESSION['message'] = "Votre compte a été créé avec succès. Vous êtes connecté.";
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
    <title>Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white flex items-center justify-center h-screen">

    <div class="w-96 bg-gray-800 p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-center text-red-400">Créer un compte</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-500 text-center"><?= $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="post" class="mt-4 space-y-4">
            <input type="text" name="first-name" placeholder="Prénom" required
                class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-md">

            <input type="text" name="last-name" placeholder="Nom" required
                class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-md">

            <input type="email" name="email" placeholder="Email" required
                class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-md">

            <input type="password" name="password" placeholder="Mot de passe" required
                class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-md">

            <select name="role" class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-md">
                <option value="member">Membre</option>
                <option value="admin">Admin</option>
            </select>

            <div class="flex justify-between">
                <a href="index.php" class="bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-500">Annuler</a>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                    S'inscrire
                </button>
            </div>
        </form>
    </div>

</body>

</html>