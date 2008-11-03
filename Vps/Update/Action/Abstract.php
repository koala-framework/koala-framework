<?php
abstract class Vps_Update_Action_Abstract
{
    public function __construct(array $options = array())
    {
        foreach ($options as $k=>$o) {
            $this->$k = $o;
        }
        $this->_init();
    }

    protected function _init()
    {
    }

    public function preUpdate()
    {
    }

    abstract public function update();
}
