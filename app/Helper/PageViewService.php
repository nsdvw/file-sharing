<?php
namespace Storage\Helper;

use Storage\Mapper\CommentMapper;
use Storage\Mapper\UserMapper;

class PageViewService
{
    private $commentMapper;
    private $userMapper;

    public function __construct(CommentMapper $commentMapper, UserMapper $userMapper)
    {
        $this->commentMapper = $commentMapper;
        $this->userMapper = $userMapper;
    }

    public function getCommentsAndAuthors($fileID)
    {
        $comments = $this->commentMapper->getComments($fileID);
        if (count($comments) == 0) {
            return [];
        }
        foreach ($comments as $comment) {
            $authorsIDs[] = $comment->author_id;
        }
        $authors = $this->userMapper->findAllByIDindexed($authorsIDs);
        $commentsAndAuthors = [];
        foreach ($comments as $comment) {
            if ($comment->author_id == null) {
                $commentsAndAuthors[] = ['comment'=>$comment, 'author'=>null];
            } else {
                $commentsAndAuthors[] =
                ['comment'=>$comment, 'author'=>$authors[$comment->author_id]];
            }
        }
        return $commentsAndAuthors;
    }
}
