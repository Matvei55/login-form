<?php
namespace App\DTO;
class CreatePostDTO
{
    public function __construct(
        public string $title,
        public string $content,
        public array $tags = [],
        public ?int $authorId = null,
        public ?int $categoryId = null
    ) {
        $this->validate();
    }
    private function validate(): void
    {
        if (empty($this->title)) {
            throw new \InvalidArgumentException('заголовок обязателен');
        }
        if(mb_strlen($this->title) < 3) {
            throw new \InvalidArgumentException('заголовок минимум 3 символа');
        }
        if (empty($this->content)) {
            throw new \InvalidArgumentException('содержание обязательно');
        }
    }
}