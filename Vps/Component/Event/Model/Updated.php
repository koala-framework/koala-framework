<?php
class Vps_Component_Event_Model_Updated extends Vps_Component_Event_Abstract
{
    public function __construct(Vps_Model_Abstract $model)
    {
        $this->setClass(get_class($model));
    }
}