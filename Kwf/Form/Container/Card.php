<?php
class Vps_Form_Container_Card extends Vps_Form_Container_Abstract
{
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setBaseCls('x-plain');
        $this->setAutoHeight(true);
    }
}
