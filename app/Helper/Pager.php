<?php namespace Storage\Helper;

class Pager
{
    const PER_PAGE = 3;
    const COUNTER_LINKS = 6;
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

        $this->firstPage =
            ($this->currentPage + self::COUNTER_LINKS - 1 <= $this->pageCount)
            ? $this->currentPage
            : $this->pageCount - self::COUNTER_LINKS + 1;

        $this->lastPage =
            ($this->currentPage + self::COUNTER_LINKS - 1 <= $this->pageCount)
            ? $this->firstPage + self::COUNTER_LINKS - 1
            : $this->pageCount;
    }
}