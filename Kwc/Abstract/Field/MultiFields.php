<?php
class Kwc_Abstract_Field_MultiFields extends Kwf_Form_Field_MultiFields
{
    public function __construct($class)
    {
        parent::__construct(Kwc_Abstract::createChildModel($class));
    }
}
