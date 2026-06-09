<?php

require_once 'Repository.php';

class ProgressRepository extends Repository {

    public function getUserProgress(int $userId): array {
        $stmt = $this->database->connect()->prepare('
            SELECT f.name, p.score, p.total, p.solved_at 
            FROM user_progress p
            JOIN fields f ON p.field_id = f.id
            WHERE p.user_id = :userId
            ORDER BY p.solved_at DESC
        ');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}