<?php

require_once __DIR__ . '/Repository.php';

class FieldsRepository extends Repository {

    public function getFields(): array {
        $stmt = $this->getPDO()->prepare('
            SELECT f.id, f.number, f.name, COUNT(e.id) AS exercises_count
            FROM fields f
            LEFT JOIN exercises e ON e.field_id = f.id
            WHERE f.number <> :mixedFieldNumber
            GROUP BY f.id, f.number, f.name
            ORDER BY f.id ASC
        ');

        $mixedFieldNumber = '0';
        $stmt->bindParam(':mixedFieldNumber', $mixedFieldNumber, PDO::PARAM_STR);
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
