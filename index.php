<?php
// Include the functions file
include 'functions.php';

if (isset($_POST["submit"])) {
    // Get form data
    $description = trim($_POST["description"]);
    $titre = trim($_POST["titre"]);
    $ddf = trim($_POST["date-de-fin"]);
    $priority = trim($_POST["priority"]);

    // Validate data
    $taskData = [
        'titre' => $titre,
        'description' => $description,
        'date-de-fin' => $ddf,
        'priority' => $priority
    ];
    $errors = validateTaskData($taskData);

    // If no errors, add task to CSV
    if (empty($errors)) {
        $fileName = 'bdd.csv';
        $taskDataArray = [$titre, $description, $ddf, $priority];
        if (addTaskToCSV($fileName, $taskDataArray)) {
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Failed to open the file for writing";
        }
    }

    // Display errors if any
    if (!empty($errors)) {
        echo "<ul>";
        foreach ($errors as $err) {
            echo "<li>" . htmlspecialchars($err) . "</li>";
        }
        echo "</ul>";
    }
} elseif (isset($_POST["remove"])) {
    // Handle task removal
    $indexToRemove = intval($_POST["remove"]);
    removeTaskFromCSV('bdd.csv', $indexToRemove);
    header("Location: index.php");
    exit();
}

$tasks = readTasksFromCSV('bdd.csv');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TODO List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="main">
        <h1 class="title">Ma TODOLIST</h1>
        
        <div class="task-list">
            <h2 class="task-title">Liste des tâches</h2>
            <?php if (!empty($tasks)): ?>
                <ul>
                    <?php foreach ($tasks as $index => $task): ?>
                        <li class="task">
                            <strong title="<?php echo htmlspecialchars($task[1]); ?>">Titre:</strong>
                            <p title="<?php echo htmlspecialchars($task[1]); ?>"><?php echo htmlspecialchars($task[0]); ?></p>
                            <strong>Date de fin:</strong><span><?php echo htmlspecialchars($task[2]); ?></span>
                            <strong>Priorité:</strong> 
                            <?php if($task[3] == 'Basse') {
                                echo '<span style="color: green;">' . htmlspecialchars($task[3]) . '</span>';
                            } elseif ($task[3] == 'Normale') {
                                echo '<span style="color: orange;">' . htmlspecialchars($task[3]) . '</span>';
                            } elseif ($task[3] == 'Haute') {
                                echo '<span style="color: red;">' . htmlspecialchars($task[3]) . '</span>';
                            } ?>
                            <form action="index.php" method="post" style="display:inline;">
                                <input type="hidden" name="remove" value="<?php echo $index; ?>">
                                <input class="remove" type="submit" value="Supprimer">
                            </form>
                            <?php 
                            $today = strtotime(date('Y-m-d'));
                            $dueDate = strtotime($task[2]);
                            
                            if($dueDate < $today) {
                                echo '<strong style="color: red;">EN RETARD</strong>';
                            } ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucune tâche trouvée.</p>
            <?php endif; ?>
        </div>

        <div class="form-container">
            <h2 class="form-title">Nouvelle tâche</h2>
            <form class="traitement" action="index.php" method="post">
                <!-- Titre -->
                <div class="form-properties">
                    <label for="titre">Titre</label>
                    <input type="text" id="titre" name="titre" placeholder="Titre de la tâche" required>
                </div>
                <!-- Description -->
                <div class="form-properties">
                    <label for="description">Description</label>
                    <input type="text" id="description" name="description" placeholder="Description de la tâche">
                </div>
                <!-- Date de fin -->
                <div class="form-properties">
                    <label for="date-de-fin">Date de fin</label>
                    <input type="date" id="date-de-fin" name="date-de-fin" required>
                </div>
                <!-- Priorité -->
                <div class="form-properties">
                    <h3 class="priority-title">Priorité</h3>
                    <div class="priority-container">
                        <input class="radio" type="radio" id="basse" name="priority" value="Basse">
                        <label for="basse">Basse</label>
                        <input class="radio" type="radio" id="normale" name="priority" value="Normale">
                        <label for="normale">Normale</label>
                        <input class="radio" type="radio" id="haute" name="priority" value="Haute">
                        <label for="haute">Haute</label>
                    </div>
                </div>
                <input type="submit" name="submit" value="Ajouter">
            </form>
        </div>
    </main>
</body>
</html>
