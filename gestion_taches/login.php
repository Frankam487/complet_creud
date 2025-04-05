<?php
require 'config.php';

if (isLoggedIn()) {
  header('Location: index.php');
  exit;
}

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $mot_de_passe = $_POST['mot_de_passe'];

  $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
  $stmt->execute(['email' => $email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    header('Location: index.php');
    exit;
  } else {
    $error = "Email ou mot de passe incorrect.";
  }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Connexion</h2>
    <?php if (isset($error)): ?>
      <p class="text-red-500 mb-4"><?= $error; ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="email" name="email" placeholder="Email" class="w-full p-2 mb-4 border rounded" required>
      <input type="password" name="mot_de_passe" placeholder="Mot de passe" class="w-full p-2 mb-4 border rounded" required>
      <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Se connecter</button>
    </form>
    <p class="mt-4 text-center">Pas de compte ? <a href="register.php" class="text-blue-500 hover:underline">S'inscrire</a></p>
  </div>
  <script src="./frank.js"></script>
</body>

</html>