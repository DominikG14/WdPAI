<?php

require_once 'Repository.php';

class ProgressRepository extends Repository {

    public function getUserProgress(int $userId): array {
        $stmt = $this->database->connect()->prepare('
            SELECT COALESCE(f.name, \'Mieszane\') AS name, p.score, p.total, p.solved_at 
            FROM user_progress p
            LEFT JOIN fields f ON p.field_id = f.id
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
                f.number AS number,
                f.name AS name,
                COUNT(p.id) AS attempts_count,
                COALESCE(SUM(p.score), 0) AS score_sum,
                COALESCE(SUM(p.total), 0) AS total_sum,
                MAX(p.solved_at) AS last_solved_at
            FROM user_progress p
            JOIN fields f ON p.field_id = f.id
            WHERE p.user_id = :userId
            GROUP BY f.id, f.number, f.name
            ORDER BY f.number ASC
        ');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
