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

    public function getUserProgressByField(int $userId): array {
        $stmt = $this->database->connect()->prepare('
            SELECT
                f.number,
                f.name,
                COUNT(p.id) AS attempts_count,
                COALESCE(SUM(p.score), 0) AS score_sum,
                COALESCE(SUM(p.total), 0) AS total_sum,
                MAX(p.solved_at) AS last_solved_at
            FROM fields f
            LEFT JOIN user_progress p ON p.field_id = f.id AND p.user_id = :userId
            WHERE f.number <> :mixedFieldNumber
            GROUP BY f.id, f.number, f.name
            ORDER BY f.id ASC
        ');
        $mixedFieldNumber = '0';
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':mixedFieldNumber', $mixedFieldNumber, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
