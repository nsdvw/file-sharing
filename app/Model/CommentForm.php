<?php

namespace Storage\Model;

class CommentForm extends Form
{
    public $contents;
    public $author_id;
    public $file_id;
    public $reply_id;

    public function rules()
    {
        return array(
            'contents' => array('notEmpty'=>true, 'maxLength'=>10000,),
        );
    }
}
