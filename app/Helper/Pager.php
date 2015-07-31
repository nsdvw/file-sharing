<?php namespace Storage\Helper;

class Pager
{
    const PER_PAGE = 20;
    const LINKS_COUNT = 6;
    public $currentPage;
    public $pageCount;
    public $firstPage;
    public $lastPage;
    protected $connection;

    public function __construct(\PDO $connection, $currentPage = 1)
    {
        $this->connection = $connection;
        $this->currentPage = $currentPage;

        $sql = "SELECT COUNT(*) as page_count FROM file";
        $sth = $this->connection->prepare($sql);
        $sth->execute();
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
        $this->pageCount = intval(ceil($res['page_count'] / self::PER_PAGE));

        $linksCount = (self::LINKS_COUNT > $this->pageCount) ? $this->pageCount
            : self::LINKS_COUNT;
        $this->firstPage =
            ($this->currentPage + $linksCount - 1 <= $this->pageCount)
            ? $this->currentPage
            : $this->pageCount - $linksCount + 1;
        $this->lastPage =
            ($this->currentPage + $linksCount - 1 <= $this->pageCount)
            ? $this->firstPage + $linksCount - 1
            : $this->pageCount;
    }
}
