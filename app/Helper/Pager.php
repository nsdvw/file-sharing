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
        $this->linksCount = $this->getLinksCount();
        $this->firstPage = $this->getFirstPage();
        $this->lastPage = $this->getLastPage();
        /* хуита какая-то, сам вижу, но не знаю, как исправить.
        знал бы как, сделал бы */
    }

    protected function getPageCount()
    {
        return intval(ceil($this->mapper->getFileCount() / static::$perPage));
    }

    protected function getLinksCount()
    {
        return ($this->linksCount > $this->pageCount)
            ? $this->pageCount
            : $this->linksCount;
    }

    protected function getFirstPage()
    {
        return ($this->currentPage + $this->linksCount - 1 <= $this->pageCount)
            ? $this->currentPage
            : $this->pageCount - $this->linksCount + 1;
    }

    protected function getLastPage()
    {
        return ($this->currentPage + $this->linksCount - 1 <= $this->pageCount)
            ? $this->firstPage + $this->linksCount - 1
            : $this->pageCount;
    }
}
