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
}
