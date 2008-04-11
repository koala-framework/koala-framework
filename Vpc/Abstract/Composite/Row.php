<?php
class Vpc_Abstract_Composite_Row extends Vpc_Row
{
    protected function _delete()
    {
        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(),
                                            'childComponentClasses');
        foreach ($classes as $k=>$i) {
            Vpc_Admin::getInstance($i)->delete($this->component_id.'-'.$k);
        }
    }
}
