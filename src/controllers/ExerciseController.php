<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repositories/ExercisesRepository.php';

class ExerciseController extends AppController {

    public function field(string $id) {
        $exercisesRepository = new ExercisesRepository();
        
        // Pobieramy zadania jako zwykłą tablicę (bez używania modeli)
        $exercises = $exercisesRepository->getExercisesByField((int)$id);

        // Renderujemy widok o nazwie "exercises" i przekazujemy tam pobrane zadania
        return $this->render('exercises', [
            'exercises' => $exercises
        ]);
    }
}