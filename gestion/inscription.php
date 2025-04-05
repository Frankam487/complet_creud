<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

  $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    'username' => $username,
    'email' => $email,
    'password' => $password,
  ]);

  echo "Inscription réussie !";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="./src/output.css">
  <title>Document</title>
</head>

<body>


  <form method="POST" class="max-w-sm mx-auto bg-white p-6 rounded-md shadow-md">
    <label for="username">Nom d'utilisateur</label>
    <input type="text" id="username" name="username" required class="w-full mt-2 p-2 border border-gray-300 rounded-md" />
    <label for="email" class="mt-4">Email</label>
    <input type="email" id="email" name="email" required class="w-full mt-2 p-2 border border-gray-300 rounded-md" />
    <label for="password" class="mt-4">Mot de passe</label>
    <input type="password" id="password" name="password" required class="w-full mt-2 p-2 border border-gray-300 rounded-md" />
    <button type="submit" class="mt-4 bg-blue-500 text-white p-2 rounded-md w-full">S'inscrire</button>
  </form>
</body>

</html>