<?php
require 'config.php';

if (!isLoggedIn()) {
  header('Location: login.php');
  exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM taches WHERE id = :id" . (isAdmin() ? "" : " AND utilisateur_id = :user_id"));
$stmt->execute(isAdmin() ? ['id' => $id] : ['id' => $id, 'user_id' => $_SESSION['user_id']]);
$tache = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tache) {
  header('Location: index.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $titre = $_POST['titre'];
  $description = $_POST['description'] ?? null;
  $type = $_POST['type'];
  $etat = $_POST['etat'];
  $date_echeance = $_POST['date_echeance'] ?: null;

  if ($tache['etat'] !== $etat) {
    $stmt = $pdo->prepare("INSERT INTO historique (tache_id, etat_precedent, nouvel_etat) VALUES (:tache_id, :etat_precedent, :nouvel_etat)");
    $stmt->execute(['tache_id' => $id, 'etat_precedent' => $tache['etat'], 'nouvel_etat' => $etat]);
  }

  $stmt = $pdo->prepare("UPDATE taches SET titre = :titre, description = :description, type = :type, etat = :etat, date_echeance = :date_echeance WHERE id = :id");
  $stmt->execute([
    'titre' => $titre,
    'description' => $description,
    'type' => $type,
    'etat' => $etat,
    'date_echeance' => $date_echeance,
    'id' => $id
  ]);
  header('Location: index.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier Tâche</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans">
  <main class="container mx-auto mt-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Modifier Tâche</h2>
    <form action="" method="POST" class="bg-white p-6 rounded-lg shadow-md">
      <input type="text" name="titre" value="<?php echo htmlspecialchars($tache['titre']); ?>" class="w-full p-2 mb-4 border rounded" required>
      <textarea name="description" class="w-full p-2 mb-4 border rounded"><?php echo htmlspecialchars($tache['description'] ?? ''); ?></textarea>
      <select name="type" class="w-full p-2 mb-4 border rounded" required>
        <option value="simple" <?php echo $tache['type'] === 'simple' ? 'selected' : ''; ?>>Tâche Simple</option>
        <option value="complexe" <?php echo $tache['type'] === 'complexe' ? 'selected' : ''; ?>>Tâche Complexe</option>
        <option value="recurente" <?php echo $tache['type'] === 'recurente' ? 'selected' : ''; ?>>Tâche Récurrente</option>
      </select>
      <select name="etat" class="w-full p-2 mb-4 border rounded" required>
        <option value="en_attente" <?php echo $tache['etat'] === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
        <option value="en_cours" <?php echo $tache['etat'] === 'en_cours' ? 'selected' : ''; ?>>En cours</option>
        <option value="terminee" <?php echo $tache['etat'] === 'terminee' ? 'selected' : ''; ?>>Terminée</option>
      </select>
      <input type="datetime-local" name="date_echeance" value="<?php echo $tache['date_echeance'] ? date('Y-m-d\TH:i', strtotime($tache['date_echeance'])) : ''; ?>" class="w-full p-2 mb-4 border rounded">
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Mettre à jour</button>
    </form>
  </main>
  <script src="./frank.js"></script>
</body>

</html>