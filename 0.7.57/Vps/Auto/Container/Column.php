<?php
class Vps_Auto_Container_Column extends Vps_Auto_Container_Abstract
{
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setBaseCls('x-plain');
    }
}
