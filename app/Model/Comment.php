<?php

namespace Storage\Model;

use Storage\Mapper\CommentMapper;

class Comment
{
    public $id;
    public $contents;
    public $file_id;
    public $author_id;
    public $materialized_path;
    public $added;
    public $level;

    public function fromForm(CommentForm $form)
    {
        $this->contents = $form->contents;
        $this->file_id = $form->file_id;
        $this->author_id = $form->author_id;
        $db_config = parse_ini_file(\BASE_DIR.'/config.ini');
        $connection = new \PDO(
                    $db_config['conn'],
                    $db_config['user'],
                    $db_config['pass']
                );
        $commentMapper = new CommentMapper($connection);
        if (!$form->reply_id) {
            $lastCommentPath = $commentMapper->getLastCommentPath($this->file_id);
            if (!$lastCommentPath) {
                $this->materialized_path = '1';
            } else {
                $explode = explode('.', $lastCommentPath['comment_path']);
                $this->materialized_path = strval(++$explode[0]);
            }
        } else {
            $parentPath = $commentMapper->getCommentPathById($form->reply_id);
            $lastReplyPath =
                $commentMapper->getLastReplyPath($parentPath['comment_path']);
            $this->materialized_path = self::incrementPath(
                                            $parentPath['comment_path'],
                                            $lastReplyPath['comment_path']
                                            );
        }
    }

    protected static function incrementPath($parentPath, $lastReplyPath)
    {
        $endOfPath = mb_substr(
                            $lastReplyPath,
                            mb_strlen($parentPath)
                        );
        if($endOfPath == '') {
           return $parentPath . '.1';
        } else {
            $explode = explode('.', $endOfPath);
            return $parentPath .'.'. ++$explode[1];
        }
    }

    public static function getLevelFromPath($path)
    {
        return count(explode('.', $path));
    }
}
