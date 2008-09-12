<?php
class Vpc_Abstract_Composite_Admin extends Vpc_Admin
{
    public function setup()
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->_class, 'child');
        foreach ($classes as $class) {
            $admin = Vpc_Admin::getInstance($class);
            if (method_exists($admin, 'setup')) {
                $admin->setup();
            }
        }
    }
    public function delete($componentId)
    {
        parent::delete($componentId);
        $classes = Vpc_Abstract::getChildComponentClasses($this->_class, 'child');
        if (!Vpc_Abstract::getSetting($this->_class, 'tablename')) {
            //wenn komponente kein model hat unterkomponenten hier löschen
            //ansonsten erledigt das die row
            foreach ($classes as $k=>$i) {
                Vpc_Admin::getInstance($i)->delete($componentId.'-'.$k);
            }
        }
        $where = array();
    }
}
