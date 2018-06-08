<?php
namespace AFQ;

use JsonSerializable;

class Author implements JsonSerializable
{
    private $id, $name, $quotes, $quoteCount;

    public function __construct(string $id, string $name, int $quoteCount=null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->quoteCount = $quoteCount;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuoteCount(): ?int
    {
        return $this->quoteCount;
    }

    public function withName(string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    public function withQuotes(Quote ...$quotes): self
    {
        $clone = clone $this;
        $clone->quotes = $quotes;
        $clone->quoteCount = count($quotes);
        return $clone;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'quotes' => $this->getQuoteCount(),
        ];
    }
}