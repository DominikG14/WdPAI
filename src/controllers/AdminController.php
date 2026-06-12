<?php

require_once 'AppController.php';
require_once __DIR__.'/../repositories/UsersRepository.php';
require_once __DIR__.'/../repositories/ProgressRepository.php'; // Import repozytorium postępów
require_once __DIR__.'/../repositories/ExercisesRepository.php';
require_once __DIR__.'/../repositories/FieldsRepository.php';

class AdminController extends AppController {
    private $usersRepository;
    private $progressRepository;
    private $exercisesRepository;
    private $fieldsRepository;

    public function __construct() {
        parent::__construct();
        $this->usersRepository = UsersRepository::getInstance();
        $this->progressRepository = new ProgressRepository(); // Inicjalizacja
        $this->exercisesRepository = new ExercisesRepository();
        $this->fieldsRepository = new FieldsRepository();
    }

    private function checkAdmin() {
        if (!isset($_SESSION['user_id']) || !$this->usersRepository->isAdmin($_SESSION['user_id'])) {
            http_response_code(403);
            header("Location: /index");
            exit();
        }
    }

    public function users() {
        $this->checkAdmin();
        $users = $this->usersRepository->getUsers();

        return $this->render("admin-users", [
            "title" => "Panel Administratora - Użytkownicy",
            "users" => $users
        ]);
    }

    // NOWA METODA: Widok postępów konkretnego użytkownika
    public function userProgress($id) {
        $this->checkAdmin();

        $targetUsername = $this->usersRepository->getUsernameById((int)$id);
        if (!$targetUsername) {
            http_response_code(404);
            header("Location: /admin/users");
            exit();
        }

        // Używamy stworzonej wcześniej metody z ProgressRepository
        $progress = $this->progressRepository->getUserProgress((int)$id);

        return $this->render("admin-user-progress", [
            "title" => "Postępy użytkownika " . $targetUsername,
            "targetUsername" => $targetUsername,
            "progress" => $progress
        ]);
    }

    public function exercises() {
        $this->checkAdmin();

        $fields = $this->fieldsRepository->getFields();
        $exercises = $this->exercisesRepository->getAllExercises();

        return $this->render("admin-exercises", [
            "title" => "Panel Administratora - Zadania",
            "fields" => $fields,
            "exercises" => $exercises,
            "status" => null
        ]);
    }

    public function createExercise() {
        $this->checkAdmin();

        if (!$this->isPost()) {
            http_response_code(405);
            header("Location: /admin/exercises");
            exit();
        }

        $fields = $this->fieldsRepository->getFields();
        $exercises = $this->exercisesRepository->getAllExercises();
        $status = [
            'success' => false,
            'message' => ''
        ];

        $fieldId = isset($_POST['field_id']) ? (int)$_POST['field_id'] : 0;
        $type = isset($_POST['type']) ? trim($_POST['type']) : '';
        $rightAnswer = isset($_POST['right_answer']) ? trim($_POST['right_answer']) : '';

        // Walidacja pola i rodzaju
        $allowedTypes = ['ABCD', 'PF'];
        if ($fieldId <= 0 || empty($type) || !in_array($type, $allowedTypes, true)) {
            $status['message'] = 'Wybierz poprawny dział i rodzaj zadania (ABCD lub PF).';
            return $this->render('admin-exercises', compact('fields', 'status'));
        }

        if ($type === 'ABCD' && !preg_match('/^[A-D]$/', $rightAnswer)) {
            $status['message'] = 'Dla zadania typu ABCD poprawna odpowiedź musi być jedną z: A, B, C, D.';
            return $this->render('admin-exercises', compact('fields', 'status'));
        }

        if ($type === 'PF' && !preg_match('/^(PP|PF|FP|FF)$/', $rightAnswer)) {
            $status['message'] = 'Dla zadania typu PF poprawna odpowiedź musi być jedną z: PP, PF, FP, FF.';
            return $this->render('admin-exercises', compact('fields', 'status'));
        }

        $field = $this->fieldsRepository->getFieldById($fieldId);
        if (!$field) {
            $status['message'] = 'Wybrany dział nie istnieje.';
            return $this->render('admin-exercises', compact('fields', 'status'));
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $status['message'] = 'Wybierz zdjęcie zadania w formacie PNG/JPG/JPEG/GIF.';
            return $this->render('admin-exercises', compact('fields', 'status'));
        }

        $imageFile = $_FILES['image'];
        $imageTmp = $imageFile['tmp_name'];
        $imageName = basename($imageFile['name']);
        $extension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];

        if (!in_array($extension, $allowedExtensions, true)) {
            $status['message'] = 'Obsługiwane formaty obrazów to PNG, JPG, JPEG oraz GIF.';
            return $this->render('admin-exercises', compact('fields', 'status'));
        }

        if (!getimagesize($imageTmp)) {
            $status['message'] = 'Wysłany plik nie wygląda na poprawne zdjęcie.';
            return $this->render('admin-exercises', compact('fields', 'status'));
        }

        $fieldDirName = preg_replace('/[^A-Za-z0-9_-]/', '_', $field['number']);
        $destinationDir = __DIR__ . '/../../public/images/exercises/' . $fieldDirName;
        if (!is_dir($destinationDir) && !mkdir($destinationDir, 0755, true) && !is_dir($destinationDir)) {
            $status['message'] = 'Nie udało się utworzyć katalogu na obrazy.';
            return $this->render('admin-exercises', compact('fields', 'status'));
        }

        $destinationFileName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
        $destinationPath = $destinationDir . '/' . $destinationFileName;

        if (!move_uploaded_file($imageTmp, $destinationPath)) {
            $status['message'] = 'Nie udało się zapisać przesłanego pliku.';
            return $this->render('admin-exercises', compact('fields', 'status'));
        }

        $imageUrl = 'public/images/exercises/' . $fieldDirName . '/' . $destinationFileName;

        $success = $this->exercisesRepository->createExercise($fieldId, $imageUrl, $type, $rightAnswer);
        if ($success) {
            $status['success'] = true;
            $status['message'] = 'Zadanie zostało pomyślnie dodane.';
            $exercises = $this->exercisesRepository->getAllExercises();
        } else {
            $status['message'] = 'Wystąpił błąd podczas zapisywania zadania.';
        }

        return $this->render('admin-exercises', compact('fields', 'exercises', 'status'));
    }

    public function deleteUser($id) {
        $this->checkAdmin();
        
        if ((int)$id === (int)$_SESSION['user_id']) {
            return $this->users();
        }

        $this->usersRepository->deleteUser((int)$id);
        
        header("Location: /admin/users");
        exit();
    }

    public function searchUsers() {
        $this->checkAdmin();

        if (!$this->isPost()) {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Method not allowed']);
            exit();
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $payload = [];

        if (stripos($contentType, 'application/json') !== false) {
            $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        } else {
            $payload = $_POST;
        }

        $search = trim($payload['search'] ?? '');
        $users = $this->usersRepository->searchUsers($search);

        header('Content-Type: application/json');
        echo json_encode(array_map(function (User $user) {
            return [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail()
            ];
        }, $users));
        exit();
    }
}
