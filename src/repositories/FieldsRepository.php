<?php

require_once __DIR__ . '/Repository.php';

class FieldsRepository extends Repository {

    /**
     * Get all non-mixed fields with their available exercise count.
     *
     * @return array<int,array<string,mixed>> Field rows.
     */
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

    /**
     * Find a field by its public number.
     *
     * @param string $number Field number such as I, II, or 0.
     * @return array<string,mixed>|null Field row or null when missing.
     */
    public function getFieldByNumber(string $number): ?array {
        $stmt = $this->getPDO()->prepare('
            SELECT id, number, name
            FROM fields
            WHERE number = :number
            LIMIT 1
        ');
        $stmt->bindParam(':number', $number, PDO::PARAM_STR);
        $stmt->execute();

        $field = $stmt->fetch(PDO::FETCH_ASSOC);
        return $field !== false ? $field : null;
    }

    /**
     * Find a field by database identifier.
     *
     * @param int $id Field identifier.
     * @return array<string,mixed>|null Field row or null when missing.
     */
    public function getFieldById(int $id): ?array {
        $stmt = $this->getPDO()->prepare('
            SELECT id, number, name
            FROM fields
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $field = $stmt->fetch(PDO::FETCH_ASSOC);
        return $field !== false ? $field : null;
    }
}
