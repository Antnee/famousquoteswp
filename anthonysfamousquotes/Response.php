<?php
namespace AFQ;

use Generator;

class Response
{
    private $body;
    private $error;
    private $headers;
    private $rawResponse;
    private $status;

    public function __construct(array $response)
    {
        $this->rawResponse = $response;
        $this->headers = $response['headers']['data'];
        $this->body = json_decode($response['body']);
        $this->status = $response['response'];
    }

    public function getHeaders(): Generator
    {
        foreach ($this->headers as $key=>$value) {
            yield [$key=>$value];
        }
    }

    /**
     * @return array|bool|null|\stdClass
     */
    public function getBody()
    {
        return $this->body->content;
    }

    public function isError(): bool
    {
        return !is_null($this->body->error) || $this->getStatus() >= 300;
    }

    public function getError(): ?object
    {
        return $this->body->error;
    }

    public function getStatus(): int
    {
        return $this->status['code'];
    }

    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }
}