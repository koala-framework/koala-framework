<?php
class Vpc_Abstract_Composite_TreeCache extends Vps_Component_Generator_Static
{
    protected $_classes = array();

    protected function _init()
    {
        parent::_init();
        $cls = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        foreach ($cls as $id=>$class) {
            if ($class) {
                $this->_classes[$id] = array(
                    'componentClass' => $class
                );
            }
        }
    }
}
