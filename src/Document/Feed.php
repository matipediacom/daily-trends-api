<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document]
#[ApiResource]
class Feed
{
    #[ODM\Id(type: 'string')]
    protected string $id;

    #[ODM\Field]
    #[Assert\NotBlank(message: "The title cannot be empty.")]
    #[Assert\Length(
        min: 5,
        max: 140,
        minMessage: "The title must be at least {{ limit }} characters long.",
        maxMessage: "The title cannot exceed {{ limit }} characters."
    )]
    protected string $title;

    #[ODM\Field]
    #[Assert\NotBlank(message: "The newspaper name cannot be empty.")]
    #[Assert\Choice(
        choices: ["Avantio news"],
        message: "The selected newspaper name '{{ value }}' is invalid. Valid options are: {{ choices }}."
    )]
    protected string $newspaperName;

    #[ODM\Field]
    #[Assert\NotBlank(message: "The newspaper key cannot be empty.")]
    #[Assert\Choice(
        choices: ['avantio'],
        message: "The selected newspaper key '{{ value }}' is invalid. Valid options are: {{ choices }}."
    )]
    protected string $newspaperKey;

    #[ODM\Field]
    #[Assert\NotBlank(message: "The publication date cannot be empty.")]
    #[Assert\Type(
        type: DateTime::class,
        message: "The publication date must be a valid DateTime instance."
    )]
    #[Assert\LessThanOrEqual(
        "now",
        message: "The publication date cannot be in the future."
    )]
    protected DateTime $publishedAt;

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

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
