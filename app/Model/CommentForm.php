<?php
namespace Storage\Model;

class CommentForm extends Form
{
    public $contents;
    public $author_id;
    public $file_id;
    public $reply_id;
    protected $fields = ['contents', 'author_id', 'file_id', 'reply_id'];

    public function rules()
    {
        return [
            'contents' => ['notEmpty' => true, 'maxLength' => 10000]
        ];
    }
}
