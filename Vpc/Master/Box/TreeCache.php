<?php
class Vpc_Master_Box_TreeCache extends Vps_Component_Generator_StaticBox
{
    protected $_classes = array();

    protected function _init()
    {
        parent::_init();
        $cls = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        foreach ($cls as $id => $class) {
            $this->_classes[$id] = array(
                'box' => $id,
                'componentClass' => $class,
                'priority' => 0,
                'inherit' => false
            );
        }
    }
}
