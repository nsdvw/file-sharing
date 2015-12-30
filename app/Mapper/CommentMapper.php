<?php
namespace Storage\Mapper;

use Storage\Model\Comment;

class CommentMapper extends AbstractMapper
{
    public function getComments($file_id)
    {
        $sql = "SELECT id, contents, file_id, author_id, materialized_path, added
                FROM comment WHERE file_id=:file_id ORDER BY materialized_path";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':file_id', $file_id, \PDO::PARAM_INT);
        $sth->execute();
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\Storage\Model\Comment');
        $comments = $sth->fetchAll();
        foreach ($comments as $comment) {
            $comment->level = $this->getLevel($comment->materialized_path);
        }
        return $comments;
    }

    public function save(Comment $comment)
    {
        $this->calculateMPath($comment);
        $sql = "INSERT INTO comment (
                    contents,
                    file_id,
                    author_id,
                    materialized_path,
                    parent_id
                ) VALUES (
                    :contents,
                    :file_id,
                    :author_id,
                    :materialized_path,
                    :parent_id
                )";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':contents', $comment->contents, \PDO::PARAM_STR);
        $sth->bindValue(':file_id', $comment->file_id, \PDO::PARAM_INT);
        $sth->bindValue(':author_id', $comment->author_id, \PDO::PARAM_INT);
        $sth->bindValue(':materialized_path',
            $comment->materialized_path,
            \PDO::PARAM_STR
        );
        $sth->bindValue(':parent_id', $comment->parent_id, \PDO::PARAM_STR);
        $sth->execute();
        $comment->id = $this->connection->lastInsertId();
    }

    public function getLastCommentPath($file_id)
    {
        $sql = "SELECT MAX(materialized_path) AS comment_path FROM comment
                WHERE file_id=:file_id";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':file_id', $file_id, \PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchColumn();
    }

    public function getCommentPathById($comment_id)
    {
        $sql = "SELECT materialized_path AS comment_path
                FROM comment WHERE id=:id";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':id', $comment_id, \PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchColumn();
    }

    public function getLastReplyPath($parent_path)
    {
        $sql = "SELECT MAX(materialized_path) AS comment_path FROM comment
                WHERE materialized_path LIKE CONCAT(:parentpath, '%')";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':parentpath', $parent_path, \PDO::PARAM_STR);
        $sth->execute();
        return $sth->fetchColumn();
    }

    private function calculateMPath(Comment $comment)
    {
        if (!$comment->parent_id) {
            $lastCommentPath = $this->getLastCommentPath($comment->file_id);
            if (!$lastCommentPath) {
                $comment->materialized_path = '1';
            } else {
                $explode = explode('.', $lastCommentPath);
                $comment->materialized_path = strval(++$explode[0]);
            }
        } else {
            $parentPath = $this->getCommentPathById($comment->parent_id);
            $lastReplyPath = $this->getLastReplyPath($parentPath);
            $comment->materialized_path =
                $this->incrementPath($parentPath, $lastReplyPath);
        }
    }

    private function incrementPath($parentPath, $lastReplyPath)
    {
        $endOfPath = mb_substr($lastReplyPath, mb_strlen($parentPath));
        if($endOfPath == '') {
           return $parentPath . '.1';
        } else {
            $explode = explode('.', $endOfPath);
            return $parentPath .'.'. ++$explode[1];
        }
    }

    private function getLevel($path)
    {
        return count(explode('.', $path));
    }
}
