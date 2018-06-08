<?php
namespace AFQ;

use JsonSerializable;

class Quote implements JsonSerializable
{
    private $id, $text, $author;

    public function __construct(string $id, string $text, Author $author)
    {
        $this->id = $id;
        $this->text = $text;
        $this->author = $author;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function withAuthor(Author $author): self
    {
        $clone = clone $this;
        $clone->author = $author;
        return $clone;
    }

    public function withText(string $text): self
    {
        $clone = clone $this;
        $clone->text = $text;
        return $clone;
    }

    public function jsonSerialize ()
    {
        return [
            'id' => $this->getId(),
            'text' => $this->getText(),
            'author' => $this->getAuthor(),
        ];
    }
}