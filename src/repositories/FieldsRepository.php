<?php

require_once __DIR__ . '/Repository.php';

class FieldsRepository extends Repository {

    public function getFields(): array {
        $stmt = $this->getPDO()->prepare('
            SELECT id, number, name 
            FROM fields 
            ORDER BY id ASC
        ');
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}