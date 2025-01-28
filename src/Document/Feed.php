<?php

namespace App\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class Feed
{
    #[MongoDB\Id]
    protected string $id;

    #[MongoDB\Field(type: 'string')]
    protected string $title;

    #[MongoDB\Field(type: 'string')]
    protected string $newspaperName;

    #[MongoDB\Field(type: 'string')]
    protected string $newspaperKey;

    #[MongoDB\Field(type: 'date')]
    protected DateTime $publishedAt;

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setNewspaperKey(string $newspaperKey): self
    {
        $this->newspaperKey = $newspaperKey;

        return $this;
    }

    public function getNewspaperKey(): string
    {
        return $this->newspaperKey;
    }

    public function setPublishedAt(DateTime $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
    }

    public function setNewspaperName(string $newspaperName): self
    {
        $this->newspaperName = $newspaperName;

        return $this;
    }

    public function getNewspaperName(): string
    {
        return $this->newspaperName;
    }
}
