<?php
class Vps_Auto_Vpc_NonTableForm extends Vps_Auto_NonTableForm
{
    public function __construct($name, $class = null, $id = null)
    {
        if ($class) $this->setProperty('class', $class);
        parent::__construct($name, $id);
    }
}
