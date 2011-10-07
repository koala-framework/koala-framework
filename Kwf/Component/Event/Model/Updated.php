<?php
class Kwf_Component_Event_Model_Updated extends Kwf_Component_Event_Abstract
{
    public function __construct(Kwf_Model_Abstract $model)
    {
        $this->class = get_class($model);
    }
}