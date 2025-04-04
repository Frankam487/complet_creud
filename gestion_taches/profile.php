<?php
require 'config.php';

if (!isLoggedIn()) {
  header('Location: bb.php');
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nom = $_POST['nom'];
  $email = $_POST['email'];

  if (!empty($_FILES['image']['name'])) {
    $image = $_FILES['image'];
    $target = 'uploads/' . basename($image['name']);
    move_uploaded_file($image['tmp_name'], $target);
  } else {
    $target = $user['image_profil'];
  }

  $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = :nom, email = :email, image_profil = :image WHERE id = :id");
  $stmt->execute(['nom' => $nom, 'email' => $email, 'image' => $target, 'id' => $_SESSION['user_id']]);
  header('Location: profile.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans">
  <nav class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
      <h1 class="text-xl font-bold">Gestion de Tâches 2025</h1>
      <div class="space-x-4">
        <a href="index.php" class="hover:underline">Tâches</a>
        <a href="logout.php" class="hover:underline">Déconnexion</a>
      </div>
    </div>
  </nav>

  <main class="container mx-auto mt-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Mon Profil</h2>
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-6">
      <img src="<?= $user['image_profil']; ?>" alt="Profil" class="w-24 h-24 rounded-full object-cover">
      <form method="POST" enctype="multipart/form-data" class="flex-1">
        <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']); ?>" class="w-full p-2 mb-4 border rounded" required>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" class="w-full p-2 mb-4 border rounded" required>
        <input type="file" name="image" class="w-full p-2 mb-4 border rounded">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Mettre à jour</button>
      </form>
    </div>
  </main>
  <script src="./frank.js"></script>
</body>

</html>