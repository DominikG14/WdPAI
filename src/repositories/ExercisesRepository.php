<?php

require_once __DIR__ . '/Repository.php';

class ExercisesRepository extends Repository {

    public function getExercisesByField(int $fieldId, ?int $limit = null): array {
        // Używamy getPDO() z Twojej klasy bazowej
        $sql = '
            SELECT id, image_url, type, right_answer 
            FROM exercises 
            WHERE field_id = :fieldId
            ORDER BY RANDOM()
        ';

        if ($limit !== null && $limit > 0) {
            $sql .= ' LIMIT :limit';
        }

        $stmt = $this->getPDO()->prepare($sql);
        
        $stmt->bindParam(':fieldId', $fieldId, PDO::PARAM_INT);

        if ($limit !== null && $limit > 0) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        // Zwracamy czystą tablicę asocjacyjną
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRandomExercises(int $limit = 10): array {
        $sql = '
            SELECT id, image_url, type, right_answer
            FROM exercises
            ORDER BY RANDOM()
            LIMIT :limit
        ';

        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveProgress(int $userId, int $fieldId, int $score, int $total): bool {
        $stmt = $this->getPDO()->prepare('
            INSERT INTO user_progress (user_id, field_id, score, total)
            VALUES (:userId, :fieldId, :score, :total)
        ');
        
        return $stmt->execute([
            'userId' => $userId,
            'fieldId' => $fieldId,
            'score' => $score,
            'total' => $total
        ]);
    }

    public function createExercise(int $fieldId, string $imageUrl, string $type, string $rightAnswer): bool {
        $stmt = $this->getPDO()->prepare('
            INSERT INTO exercises (field_id, image_url, type, right_answer)
            VALUES (:fieldId, :imageUrl, :type, :rightAnswer)
        ');

        return $stmt->execute([
            'fieldId' => $fieldId,
            'imageUrl' => $imageUrl,
            'type' => $type,
            'rightAnswer' => $rightAnswer
        ]);
    }

    public function getAllExercises(): array {
        $sql = '
            SELECT e.id, e.field_id, e.image_url, e.type, e.right_answer,
                   f.number AS field_number, f.name AS field_name
            FROM exercises e
            LEFT JOIN fields f ON e.field_id = f.id
            ORDER BY e.id DESC
        ';

        $stmt = $this->getPDO()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
