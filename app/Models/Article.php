<?php

namespace App\Models;

/**
 * Article data structure for category pages
 * This is a simple data class, not an Eloquent model
 */
class Article
{
    public string $title;
    public string $description;
    public string $link;
    public string $difficulty; // beginner, intermediate, advanced
    public string $icon;
    public string $readTime;

    public function __construct(
        string $title,
        string $description,
        string $link,
        string $difficulty = 'beginner',
        string $icon = 'default',
        string $readTime = '5 minute read'
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->link = $link;
        $this->difficulty = $difficulty;
        $this->icon = $icon;
        $this->readTime = $readTime;
    }

    /**
     * Create article from array data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['link'] ?? '#',
            $data['difficulty'] ?? 'beginner',
            $data['icon'] ?? 'default',
            $data['read_time'] ?? '5 minute read'
        );
    }

    /**
     * Convert to array for Blade component
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'link' => $this->link,
            'difficulty' => $this->difficulty,
            'icon' => $this->icon,
            'readTime' => $this->readTime,
        ];
    }
}