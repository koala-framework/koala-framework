<?php
namespace KwfBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class NoTags extends Constraint
{
    public $message = '';

    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->message = trlKwf("Must not include tags");
    }
}
