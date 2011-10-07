<?php
abstract class Vps_Update_Action_Abstract
{
    public $silent = false;
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

    public function checkSettings()
    {
    }

    public function preUpdate()
    {
    }

    public function postUpdate()
    {
    }

    public function postClearCache()
    {
    }

    abstract public function update();
}
