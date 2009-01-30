<?php
class Vpc_Abstract_Field_MultiFields extends Vps_Form_Field_MultiFields
{
    public function __construct($class)
    {
        parent::__construct(Vpc_Abstract::createModel($class));
    }
}
