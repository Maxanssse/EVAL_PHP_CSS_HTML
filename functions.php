<?php
function addTaskToCSV($fileName, $taskData) {
    $fileExists = file_exists($fileName);
    $file = fopen($fileName, 'a');
    if ($file !== FALSE) {
        if (!$fileExists) {
            fputcsv($file, ["Titre", "Description", "Date de fin", "Priorité"]); // Initialisation du fichier csv si celui-ci n'existe pas
        }
        fputcsv($file, $taskData); // Insertion des donneés de la tâche dans le csv
        fclose($file);
        return true;
    } else {
        return false;
    }
}


function validateTaskData($data) {
    $errors = [];

    if (empty($data['titre'])) {
        $errors[] = "Un titre est requis";
    }

    if (empty($data['date-de-fin'])) {
        $errors[] = "Une date de fin est requise";
    }

    return $errors;
} // Validation de la présence des données avant insertion

function readTasksFromCSV($fileName) {
    $tasks = [];
    if (($file = fopen($fileName, 'r')) !== FALSE) {
        fgetcsv($file);
        while (($data = fgetcsv($file)) !== FALSE) {
            $tasks[] = $data; // Insertion des données dans un tableau pour l'affichage dans l'HTML
        }
        fclose($file);
    }
    return $tasks;
}

function removeTaskFromCSV($fileName, $indexToRemove) {
    $tasks = [];
    if (($file = fopen($fileName, 'r')) !== FALSE) {
        $header = fgetcsv($file);
        while (($data = fgetcsv($file)) !== FALSE) {
            $tasks[] = $data;
        }
        fclose($file);
    }

    if (isset($tasks[$indexToRemove])) {
        unset($tasks[$indexToRemove]); // Permet de retirer la tâche souhaitée du tableau afin de ne pas la réinsérer dans le csv
        $tasks = array_values($tasks); // Sert à l'indexation du tableau
    }

    $file = fopen($fileName, 'w');
    if ($file !== FALSE) {
        // Write the header
        fputcsv($file, $header);
        foreach ($tasks as $task) {
            fputcsv($file, $task);
        }
        fclose($file);
    }
} // Fonction qui permet de supprimer une tâche en réecrivant l'entièreté du csv
?>
