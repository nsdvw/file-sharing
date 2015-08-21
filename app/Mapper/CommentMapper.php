<?php

namespace Storage\Mapper;

class CommentMapper
{
    protected $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getComments()
    {
        $sql = "SELECT * FROM comment";
        $sth = $this->connection->prepare($sql);
        $sth->execute();
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\Storage\Model\Comment');
        return $sth->fetchAll();
    }

    public function getLastCommentPath($file_id)
    {
        $sql = "SELECT MAX(materialized_path) AS comment_path FROM comment
                WHERE file_id=:file_id";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':file_id', $file_id, \PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetch();
    }

    public function getCommentPathById($comment_id)
    {
        $sql = "SELECT materialized_path AS comment_path
                FROM comment WHERE id=:id";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':id', $comment_id, \PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetch();
    }

    public function getLastReplyPath($parent_path)
    {
        $sql = "SELECT MAX(materialized_path) AS comment_path FROM comment
                WHERE materialized_path LIKE CONCAT(:parentpath, '%')";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':parentpath', $parent_path, \PDO::PARAM_STR);
        $sth->execute();
        return $sth->fetch();
    }
}