<?php
namespace KwfBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class NoTags extends Constraint
{
    public $message = '';

    static $trlMessage = null;
    public function __construct($options = null)
    {
        parent::__construct($options);
        if (!self::$trlMessage) { // trl-call is expensive...
            self::$trlMessage = trlKwf("Must not include tags");
        }
        $this->message = self::$trlMessage;
    }
}
