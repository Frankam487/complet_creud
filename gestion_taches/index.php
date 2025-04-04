<?php
require 'config.php';

if (!isLoggedIn()) {
  header('Location: login.php');
  exit;
}

$query = isAdmin() ? "SELECT t.*, u.nom FROM taches t LEFT JOIN utilisateurs u ON t.utilisateur_id = u.id ORDER BY t.date_creation DESC" :
  "SELECT * FROM taches WHERE utilisateur_id = :user_id ORDER BY date_creation DESC";
$stmt = $pdo->prepare($query);
if (!isAdmin()) $stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Taches</title>
  <script src="./frank.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans">
  <nav class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
      <h1 class="text-xl font-bold">Devoir de Taches</h1>
      <div class="space-x-4">
        <a href="profile.php" class="hover:underline">Profil</a>
        <a href="logout.php" class="hover:underline">Déconnexion</a>
      </div>
    </div>
  </nav>

  <main class="container mx-auto mt-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Mes Tâches</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <?php foreach (['simple' => 'Tâches Simples', 'complexe' => 'Tâches Complexes', 'recurente' => 'Tâches Récurrentes'] as $type => $titre): ?>
        <div class="bg-white p-4 rounded-lg shadow-md">
          <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo $titre; ?></h3>
          <ul class="space-y-2">
            <?php foreach ($taches as $tache): ?>
              <?php if ($tache['type'] === $type): ?>
                <li class="flex justify-between items-center hover:bg-gray-100 p-2 rounded">
                  <div>
                    <span><?php echo htmlspecialchars($tache['titre']); ?></span>
                    <?php if (isAdmin() && isset($tache['nom'])): ?>
                      <span class="text-sm text-gray-500"> (<?php echo htmlspecialchars($tache['nom']); ?>)</span>
                    <?php endif; ?>
                  </div>
                  <div class="flex space-x-2">
                    <span class="text-sm <?php echo $tache['etat'] === 'terminee' ? 'text-green-500' : ($tache['etat'] === 'en_cours' ? 'text-yellow-500' : 'text-gray-500'); ?>">
                      <?php echo ucfirst($tache['etat']); ?>
                    </span>
                    <a href="edit_task.php?id=<?php echo $tache['id']; ?>" class="text-blue-500 hover:underline">Modifier</a>
                    <a href="delete_task.php?id=<?php echo $tache['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('Supprimer cette tâche ?');">Supprimer</a>
                  </div>
                </li>
              <?php endif; ?>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    </div>

    <section class="mt-8 bg-white p-6 rounded-lg shadow-md">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Ajouter une Tâche</h3>
      <form action="add_task.php" method="POST">
        <input type="text" name="titre" placeholder="Titre" class="w-full p-2 mb-4 border rounded" required>
        <textarea name="description" placeholder="Description" class="w-full p-2 mb-4 border rounded"></textarea>
        <select name="type" class="w-full p-2 mb-4 border rounded" required>
          <option value="simple">Tâche Simple</option>
          <option value="complexe">Tâche Complexe</option>
          <option value="recurente">Tâche Récurrente</option>
        </select>
        <input type="datetime-local" name="date_echeance" class="w-full p-2 mb-4 border rounded">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Ajouter</button>
      </form>
    </section>
  </main>
  <script src="./frank.js"></script>
</body>

</html>