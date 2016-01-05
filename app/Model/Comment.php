<?php
namespace FileSharing\Model;

use FileSharing\Mapper\CommentMapper;

class Comment
{
    public $id;
    public $contents;
    public $file_id;
    public $author_id;
    public $materialized_path;
    public $added;
    public $level;
}
