<?php
session_start();
include 'db.php';


if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'user') {
  header('Location: connexion.php');
  exit();
}

$user_id = $_SESSION['user_id'];


$sql = "SELECT * FROM tasks WHERE created_by = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$tasks = $stmt->fetchAll();
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
  <div class="max-w-4xl mx-auto p-6 bg-white rounded-md shadow-md">
    <h2 class="text-xl font-semibold">Tableau de bord</h2>

    <a href="creer_tache.php" class="mt-4 inline-block bg-green-500 text-white p-2 rounded-md">Créer une tâche</a>

    <table class="mt-4 w-full table-auto border-collapse">
      <thead>
        <tr>
          <th class="border p-2">Titre</th>
          <th class="border p-2">Type</th>
          <th class="border p-2">Statut</th>
          <th class="border p-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tasks as $task): ?>
          <tr>
            <td class="border p-2"><?= htmlspecialchars($task['title']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($task['type']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($task['status']) ?></td>
            <td class="border p-2">
              <a href="modifier_tache.php?id=<?= $task['id'] ?>" class="text-blue-500">Modifier</a> |
              <a href="supprimer_tache.php?id=<?= $task['id'] ?>" class="text-red-500">Supprimer</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>

</html>