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
    public $level; // уровень вложенности, удобен для подстановки в класс css

    protected $mapper;

    public function fromForm(CommentForm $form, CommentMapper $mapper)
    {
        $this->mapper = $mapper;
        $this->contents = $form->contents;
        $this->file_id = $form->file_id;
        $this->author_id = ($form->author_id) ? intval($form->author_id) : null;
        if (!$form->reply_id) {
            $lastCommentPath = $this->mapper->getLastCommentPath($this->file_id);
            if (!$lastCommentPath) {
                $this->materialized_path = '1';
            } else {
                $explode = explode('.', $lastCommentPath['comment_path']);
                $this->materialized_path = strval(++$explode[0]);
            }
        } else {
            $parentPath = $this->mapper->getCommentPathById($form->reply_id);
            $lastReplyPath =
                $this->mapper->getLastReplyPath($parentPath['comment_path']);
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

    public static function getLevel($path)
    {
        return count(explode('.', $path));
    }
}
