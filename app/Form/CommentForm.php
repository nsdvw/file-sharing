<?php
namespace Storage\Form;

use Slim\Http\Request;
use Storage\Model\Comment;

class CommentForm extends AbstractFormWithCaptcha
{
    public $contents;
    public $author_id;
    public $file_id;
    public $reply_id;
    public $captcha;

    private $comment;

    protected $fields = ['contents', 'author_id', 'file_id', 'reply_id', 'captcha'];

    public function __construct(Request $request, $file_id, $author_id = null)
    {
        $commentData = $request->post('comment_form');
        $this->contents = isset($commentData['contents'])
                          ? $commentData['contents'] : null;
        $this->reply_id = $request->get('reply');
        $this->file_id = $file_id;
        $this->author_id = $author_id;
        $this->captcha = isset($commentData['captcha'])
                         ? $commentData['captcha'] : null;
    }

    public function rules()
    {
        return [
            'contents' =>
                ['notEmpty' => true, 'maxLength' => 10000],
            'captcha' =>
                ['captcha' => true],
        ];
    }

    public function getComment()
    {
        $this->comment = new Comment;
        $this->comment->contents = $this->contents;
        $this->comment->author_id = $this->author_id;
        $this->comment->file_id = $this->file_id;
        $this->comment->parent_id = $this->reply_id;
        return $this->comment;
    }
}
