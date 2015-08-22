<?php

namespace Storage\Model;

class Captcha extends Form
{
    public $passcode;

    public function rules()
    {
        return array(
            'passcode' => array('notEmpty'=>true, 'captcha'=>true),
            );
    }
}