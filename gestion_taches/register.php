<?php
require 'config.php';

if (isLoggedIn()) {
  header('Location: index.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nom = $_POST['nom'];
  $email = $_POST['email'];
  $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

  try {
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (:nom, :email, :mot_de_passe)");
    $stmt->execute(['nom' => $nom, 'email' => $email, 'mot_de_passe' => $mot_de_passe]);
    header('Location: login.php');
    exit;
  } catch (PDOException $e) {
    $error = "Erreur : " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Inscription</h2>
    <?php if (isset($error)): ?>
      <p class="text-red-500 mb-4"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="nom" placeholder="Nom" class="w-full p-2 mb-4 border rounded" required>
      <input type="email" name="email" placeholder="Email" class="w-full p-2 mb-4 border rounded" required>
      <input type="password" name="mot_de_passe" placeholder="Mot de passe" class="w-full p-2 mb-4 border rounded" required>
      <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">S'inscrire</button>
    </form>
    <p class="mt-4 text-center">Déjà inscrit ? <a href="login.php" class="text-blue-500 hover:underline">Se connecter</a></p>
  </div>
  <script src="./frank.js"></script>
</body>

</html>