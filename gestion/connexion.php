<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $sql = "SELECT * FROM users WHERE email = :email";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['email' => $email]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    header("Location: dashboard.php");
    exit();
  } else {
    echo "Identifiants incorrects.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="./src/input.css">
  <title>Document</title>
</head>

<body>

  <form method="POST" class="max-w-sm mx-auto bg-white p-6 rounded-md shadow-md">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required class="w-full mt-2 p-2 border border-gray-300 rounded-md" />

    <label for="password" class="mt-4">Mot de passe</label>
    <input type="password" id="password" name="password" required class="w-full mt-2 p-2 border border-gray-300 rounded-md" />

    <button type="submit" class="mt-4 bg-blue-500 text-white p-2 rounded-md w-full">Se connecter</button>
  </form>
</body>

</html>