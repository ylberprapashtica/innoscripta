<?php

namespace App\NewsFetcher;

class NewsFetcherQueryParameters
{

    /**
     * @var string The term to search for in news articles
     */
    protected string $term;
    /**
     * @var int Number of articles per page
     */
    protected int $pageSize = 10;
    /**
     * @var bool Use the biggest possible page size
     */
    protected bool $useQueryMaxPageSize = true;
    /**
     * @var int Which page to retrieve
     */
    protected int $page = 0;

    /**
     * @param string $term
     * @param int $pageSize
     * @param bool $useQueryMaxPageSize
     * @param int $page
     */
    public function __construct(string $term, int $pageSize = 10, bool $useQueryMaxPageSize = true, int $page = 0)
    {
        $this->term = $term;
        $this->pageSize = $pageSize;
        $this->useQueryMaxPageSize = $useQueryMaxPageSize;
        $this->page = $page;
    }

    public static function create(string $term): NewsFetcherQueryParameters
    {
        return new NewsFetcherQueryParameters(trim($term));
    }

    public function getTerm(): string
    {
        return $this->term;
    }

    public function setTerm(string $term): void
    {
        $this->term = $term;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function setPageSize(int $pageSize): void
    {
        $this->pageSize = $pageSize;
    }

    public function useQueryMaxPageSize(): bool
    {
        return $this->useQueryMaxPageSize;
    }

    public function setUseQueryMaxPageSize(bool $useQueryMaxPageSize): void
    {
        $this->useQueryMaxPageSize = $useQueryMaxPageSize;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

}
