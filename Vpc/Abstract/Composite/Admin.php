<?php
class Vpc_Abstract_Composite_Admin extends Vpc_Admin
{
    public function setup()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        foreach ($classes as $class) {
            Vpc_Admin::getInstance($class)->setup();
        }


        $fields['image_position'] = "enum('left', 'right', 'alternate') default NULL";
        $this->createFormTable('vpc_composite_textimage', $fields);
    }
    public function delete($class, $componentId)
    {
        parent::delete($class, $componentId);
        if (!Vpc_Abstract::getSetting($class, 'tablename')) {
            //wenn komponente kein model hat unterkomponenten hier löschen
            //ansonsten erledigt das die row
            $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
            foreach ($classes as $k=>$i) {
                Vpc_Admin::getInstance($i)->delete($componentId.'-'.$k);
            }
        }
    }
}
