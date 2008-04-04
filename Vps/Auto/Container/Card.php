<?php
class Vps_Auto_Container_Card extends Vps_Auto_Container_Abstract
{
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setLayout('form');
        $this->setBaseCls('x-plain');
        $this->setAutoHeight(true);
    }
    public function getByName($name)
    {
        $ret = parent::getByName($name);
        return $ret;
    }
}
