<?php

class Exercise {
    private $id;
    private $fieldId;
    private $imageUrl;
    private $type;
    private $rightAnswer;

    /**
     * Create an exercise data object.
     *
     * @param int $id Exercise identifier.
     * @param int $fieldId Related math field identifier.
     * @param string|null $imageUrl Relative image path.
     * @param string $type Exercise type, either ABCD or PF.
     * @param string $rightAnswer Correct answer value.
     */
    public function __construct(int $id, int $fieldId, ?string $imageUrl, string $type, string $rightAnswer) {
        $this->id = $id;
        $this->fieldId = $fieldId;
        $this->imageUrl = $imageUrl;
        $this->type = $type;
        $this->rightAnswer = $rightAnswer;
    }

    /** @return int Exercise identifier. */
    public function getId(): int {
        return $this->id;
    }

    /** @return int Related math field identifier. */
    public function getFieldId(): int {
        return $this->fieldId;
    }

    /** @return string|null Relative image path. */
    public function getImageUrl(): ?string {
        return $this->imageUrl;
    }

    /** @return string Exercise type. */
    public function getType(): string {
        return $this->type;
    }

    /** @return string Correct answer value. */
    public function getRightAnswer(): string {
        return $this->rightAnswer;
    }
}
