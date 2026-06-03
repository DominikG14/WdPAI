<?php

class Exercise {
    private $id;
    private $fieldId;
    private $imageUrl;
    private $type;
    private $rightAnswer;

    public function __construct(int $id, int $fieldId, ?string $imageUrl, string $type, string $rightAnswer) {
        $this->id = $id;
        $this->fieldId = $fieldId;
        $this->imageUrl = $imageUrl;
        $this->type = $type;
        $this->rightAnswer = $rightAnswer;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getFieldId(): int {
        return $this->fieldId;
    }

    public function getImageUrl(): ?string {
        return $this->imageUrl;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getRightAnswer(): string {
        return $this->rightAnswer;
    }
}