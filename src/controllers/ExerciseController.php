<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repositories/ExercisesRepository.php';

class ExerciseController extends AppController {

    public function field(string $id) {
        $exercisesRepository = new ExercisesRepository();
        $taskCount = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
        $taskCount = $taskCount !== null && $taskCount > 0 ? $taskCount : null;
        $exercises = $exercisesRepository->getExercisesByField((int)$id, $taskCount);

        // Uruchamiamy sesję, jeśli jeszcze nie ruszyła, żeby sprawdzić czy user jest zalogowany
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $this->render('exercises', [
            'exercises' => $exercises,
            'fieldId' => (int)$id,
            'isLoggedIn' => isset($_SESSION['user_id']), // Przekazujemy stan zalogowania do widoku
        ]);
    }

    /**
     * Akcja wywoływana przez JS (Fetch API) pod adresem /exercises/save
     */
    public function save() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Blokada, jeśli ktoś kombinuje niezalogowany
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Musisz być zalogowany']);
            exit();
        }

        // Pobranie danych JSON przesłanych z JavaScriptu
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $fieldId = (int)$data['field_id'];
        $score = (int)$data['score'];
        $total = (int)$data['total'];
        $userId = (int)$_SESSION['user_id'];

        $repo = new ExercisesRepository();
        $success = $repo->saveProgress($userId, $fieldId, $score, $total);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
    }
}
