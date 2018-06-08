<?php
namespace AFQ;

require_once ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR.'Author.php';
require_once ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR.'Quote.php';
require_once ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR.'Response.php';

require_once ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR.'exception/ApiErrorException.php';

use AFQ\Exception\ApiErrorException;

class Client
{
    const METHOD_DELETE = 'DELETE';
    const METHOD_GET = 'GET';
    const METHOD_PATCH = 'PATCH';
    const METHOD_POST = 'POST';

    private $key, $uri;

    public function __construct(string $uri, string $key)
    {
        $this->key = $key;
        $this->uri = $uri;
    }

    /**
     * Get Array of Authors
     *
     * @return array
     * @throws ApiErrorException
     */
    public function getAuthors(): array
    {
        $response = $this->makeRequest('authors');
        if ($response->isError()) {
            throw new ApiErrorException;
        }
        $authors = $response->getBody();
        $data = [];
        foreach ($authors as $a) {
            $data[] = new Author($a->id, $a->name, $a->quotes);
        }
        return $data;
    }

    /**
     * Add Author to API
     *
     * @param string $name
     * @return Author
     * @throws ApiErrorException
     */
    public function addAuthor(string $name): Author
    {
        $response = $this->makeRequest('authors', null, self::METHOD_POST, ['name'=>$name]);
        if ($response->isError()) {
            throw new ApiErrorException($response->getError()->message);
        }

        return $this->getAuthorByName($name);
    }

    /**
     * Get Single Author by Name
     *
     * @param string $name
     * @return Author
     * @throws ApiErrorException
     */
    public function getAuthorByName(string $name): Author
    {
        $response = $this->makeRequest('authors', $name);
        if ($response->isError()) {
            throw new ApiErrorException($response->getError()->message);
        }

        return new Author($response->getBody()->id, $response->getBody()->name, $response->getBody()->quotes);
    }

    /**
     * Replace Author Entity
     *
     * @param Author $oldAuthor
     * @param Author $newAuthor
     * @return Author
     * @throws ApiErrorException
     */
    public function updateAuthor(Author $oldAuthor, Author $newAuthor): Author
    {
        if ($oldAuthor->getName() !== $newAuthor->getName()) {
            $response = $this->makeRequest('authors', $oldAuthor->getName(), self::METHOD_PATCH, $newAuthor);
            if ($response->isError()) {
                throw new ApiErrorException($response->getError()->message);
            }
        }
        return $newAuthor;
    }

    /**
     * Delete Author Entity
     *
     * <strong>Warning:</strong> Also removes associated quotes
     *
     * @param Author $author
     * @return bool
     * @throws ApiErrorException
     */
    public function deleteAuthor(Author $author): bool
    {
        $response = $this->makeRequest('authors', $author->getName(), self::METHOD_DELETE);
        if ($response->isError()) {
            throw new ApiErrorException($response->getError()->message);
        }
        return true;
    }

    /**
     * Get a Random Quote
     *
     * @return Quote
     * @throws ApiErrorException
     */
    public function getRandomQuote(): Quote
    {
        $response = $this->makeRequest('quotes', 'random');
        if ($response->isError()) {
            throw new ApiErrorException;
        }
        $body = $response->getBody();

        $author = new Author($body->author->id, $body->author->name, $body->author->quotes);

        return new Quote($body->id, $body->text, $author);
    }

    /**
     * Add New Quote for Author
     *
     * @param string $quote
     * @param string $authorName
     * @return Quote
     * @throws ApiErrorException
     */
    public function addQuoteForAuthor(string $quote, string $authorName): Quote
    {
        $response = $this->makeRequest('authors', sprintf("%s/quotes", $authorName), self::METHOD_POST, ['text'=>$quote]);
        if ($response->isError()) {
            throw new ApiErrorException;
        }
        $quote = $response->getBody();
        $author = new Author($quote->author->id, $quote->author->name, $quote->author->quotes);
        return new Quote($quote->id, $quote->text, $author);
    }

    /**
     * Get Quotes for Author
     *
     * @param string $authorName
     * @return array
     * @throws ApiErrorException
     */
    public function getQuotesForAuthor(string $authorName): array
    {
        $response = $this->makeRequest('authors', sprintf("%s/quotes", $authorName));
        if ($response->isError()) {
            throw new ApiErrorException;
        }
        $quotes = $response->getBody();
        $data = [];
        $author = null;
        foreach ($quotes as $q) {
            if (!$author) {
                $author = new Author($q->author->id, $q->author->name, $q->author->quotes);
            }
            $data[] = new Quote($q->id, $q->text, $author);
        }
        return $data;
    }

    /**
     * Update Quote Text or Author
     *
     * @param Quote $oldQuote
     * @param Quote $newQuote
     * @return Quote
     * @throws ApiErrorException
     */
    public function updateQuote(Quote $oldQuote, Quote $newQuote): Quote
    {
        if ($oldQuote->getText() !== $newQuote->getText() || $oldQuote->getAuthor()->getId() !== $newQuote->getAuthor()->getId()) {
            $body = [
                'text' => $newQuote->getText(),
                'authorId' => $newQuote->getAuthor()->getId(),
            ];
            $response = $this->makeRequest('quotes', $oldQuote->getId(), self::METHOD_PATCH, $body);
            if ($response->isError()) {
                throw new ApiErrorException($response->getError()->message);
            }
        }
        return $newQuote;
    }

    /**
     * Delete Quote
     *
     * @param Quote $quote
     * @return bool
     * @throws ApiErrorException
     */
    public function deleteQuote(Quote $quote): bool
    {
        $response = $this->makeRequest('quotes', $quote->getId(), self::METHOD_DELETE);
        if ($response->isError()) {
            throw new ApiErrorException($response->getError()->message);
        }
        return true;
    }

    /**
     * Make API Request
     *
     * @param string $endpoint
     * @param string $document
     * @param string $method
     * @param object|null $body
     * @return Response
     */
    private function makeRequest(string $endpoint, string $document=null, string $method=self::METHOD_GET, $body=null): Response
    {
        $target = sprintf('%s/%s%s', $this->uri, $endpoint, $document ? '/'.$document : '');

        $args = [
            'method' => $method,
            'headers' => ['x-api-key'=>$this->key],
        ];

        if ($body) {
            $args['body'] = json_encode($body);
        }

        return new Response(wp_remote_request($target, $args));
    }
}