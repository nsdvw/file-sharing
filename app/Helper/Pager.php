<?php
namespace FileSharing\Helper;

class Pager
{
    public $currentPage;
    public $totalItemCount;
    public $perPage;
    public $linksCount;

    private $maxLinksCount;

    public function __construct(
        $currentPage,
        $totalItemCount,
        $perPage = 15,
        $maxLinksCount = 6
    ) {
        $this->currentPage = intval($currentPage);
        $this->totalItemCount = intval($totalItemCount);
        $this->perPage = $perPage;
        $this->maxLinksCount = $maxLinksCount;
        $pageCount = $this->getPageCount();
        $this->linksCount = $this->getLinksCount($pageCount);
    }

    public function getPrevPage()
    {
        return $this->currentPage - 1;
    }

    public function getNextPage()
    {
        return $this->currentPage + 1;
    }

    public function getFirstPage()
    {
        return
            $this->currentPage + $this->linksCount - 1 <= $this->getPageCount()
            ? $this->currentPage
            : $this->getPageCount() - $this->linksCount + 1;
    }

    public function getLastPage()
    {
        return
            $this->currentPage + $this->linksCount - 1 <= $this->getPageCount()
            ? $this->getFirstPage() + $this->linksCount - 1
            : $this->getPageCount();
    }

    public function getOffset()
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    private function getPageCount()
    {
        return intval(ceil($this->totalItemCount / $this->perPage));
    }

    private function getLinksCount($pageCount)
    {
        return ($this->maxLinksCount > $pageCount)
            ? $pageCount
            : $this->maxLinksCount;
    }
}
