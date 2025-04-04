<?php
require 'config.php';

if (!isLoggedIn()) {
  header('Location: login.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $titre = $_POST['titre'];
  $description = $_POST['description'] ?? null;
  $type = $_POST['type'];
  $date_echeance = $_POST['date_echeance'] ?: null;

  $stmt = $pdo->prepare("INSERT INTO taches (utilisateur_id, titre, description, type, date_echeance) VALUES (:user_id, :titre, :description, :type, :date_echeance)");
  $stmt->execute([
    'user_id' => $_SESSION['user_id'],
    'titre' => $titre,
    'description' => $description,
    'type' => $type,
    'date_echeance' => $date_echeance
  ]);
  header('Location: index.php');
  exit;
}
