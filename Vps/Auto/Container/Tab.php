<?php
class Vps_Auto_Container_Tab extends Vps_Auto_Container_Abstract
{
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setBaseCls('x-plain');
        $this->setAutoHeight(true);
        $this->setBodyStyle('padding:10px');
    }
}
