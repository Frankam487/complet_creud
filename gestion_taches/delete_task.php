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
