<?php
class Kwc_Abstract_Composite_Row extends Kwc_Row
{
    protected function _delete()
    {
        $classes = Kwc_Abstract::getChildComponentClasses($this->getTable()->getComponentClass(), 'child');
        foreach ($classes as $k=>$i) {
            Kwc_Admin::getInstance($i)->delete($this->component_id.'-'.$k);
        }
    }
}
