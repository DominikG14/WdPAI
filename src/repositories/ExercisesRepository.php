<?php

require_once __DIR__ . '/Repository.php';

class ExercisesRepository extends Repository {

    public function getExercisesByField(int $fieldId): array {
        // Używamy getPDO() z Twojej klasy bazowej
        $stmt = $this->getPDO()->prepare('
            SELECT id, image_url, type, right_answer 
            FROM exercises 
            WHERE field_id = :fieldId
            ORDER BY id ASC
        ');
        
        $stmt->bindParam(':fieldId', $fieldId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Zwracamy czystą tablicę asocjacyjną
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}