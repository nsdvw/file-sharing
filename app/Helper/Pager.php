<?php namespace Storage\Helper;

class Pager
{
    public static $perPage = 20;
    public $linksCount = 6;
    public $currentPage;
    public $pageCount;
    public $firstPage;
    public $lastPage;
    protected $mapper;

    public function __construct(
        \Storage\Mapper\FileMapper $mapper,
        $currentPage = 1, $perPage = 20, $linksCount = 6)
    {
        $this->mapper = $mapper;
        $this->currentPage = $currentPage;
        static::$perPage = $perPage;
        $this->linksCount = $linksCount;

        $this->pageCount = $this->getPageCount();

        $this->linksCount =
            ($this->linksCount > $this->pageCount)
            ? $this->pageCount
            : $this->linksCount;
        $this->firstPage =
            ($this->currentPage + $this->linksCount - 1 <= $this->pageCount)
            ? $this->currentPage
            : $this->pageCount - $this->linksCount + 1;
        $this->lastPage =
            ($this->currentPage + $this->linksCount - 1 <= $this->pageCount)
            ? $this->firstPage + $this->linksCount - 1
            : $this->pageCount;
    }

    protected function getPageCount()
    {
        return intval(ceil($this->mapper->getFileCount() / static::$perPage));
    }
}
