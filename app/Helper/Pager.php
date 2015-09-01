<?php namespace Storage\Helper;

class Pager
{
    public static $perPage = 20;
    public $linksCount = 6;
    public $currentPage;
    public $pageCount;
    public $firstPage;
    public $lastPage;
    protected $connection;

    public function __construct(\PDO $connection, $currentPage = 1, $perPage = 20, $linksCount = 6)
    {
        $this->connection = $connection;
        $this->currentPage = $currentPage;
        static::$perPage = $perPage;
        $this->linksCount = $linksCount;

        $sql = "SELECT COUNT(*) as page_count FROM file";
        $sth = $this->connection->prepare($sql);
        $sth->execute();
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
        $this->pageCount = intval(ceil($res['page_count'] / static::$perPage));

        $linksCount = ($this->linksCount > $this->pageCount) ? $this->pageCount
            : $this->linksCount;
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
