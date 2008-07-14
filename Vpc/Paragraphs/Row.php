<?php
class Vpc_Paragraphs_Row extends Vpc_Row
{
    protected function _delete()
    {
        $componentClass = $this->getTable()->getComponentClass();
        $classes = Vpc_Abstract::getChildComponentClasses($componentClass, 'paragraphs');
        if (isset($classes[$this->component])) {
            $admin = Vpc_Admin::getInstance($classes[$this->component]);
            if ($admin) {
                $admin->delete($this->component_id. '-' . $this->id);
            }
        }
    }
}
