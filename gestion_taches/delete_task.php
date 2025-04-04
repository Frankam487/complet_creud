<?php
require 'config.php';

if (!isLoggedIn()) {
  header('Location: login.php');
  exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM taches WHERE id = :id" . (isAdmin() ? "" : " AND utilisateur_id = :user_id"));
$stmt->execute(isAdmin() ? ['id' => $id] : ['id' => $id, 'user_id' => $_SESSION['user_id']]);
header('Location: index.php');
exit;

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>

  <script src="./frank.js"></script>
</body>

</html>