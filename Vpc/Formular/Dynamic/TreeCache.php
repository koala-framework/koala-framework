<?php
class Vpc_Formular_Dynamic_TreeCache extends Vpc_TreeCache_Static
{
    protected $_classes = array();

    protected function _init()
    {
        parent::_init();
        $cls = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        $this->_classes['success'] = $cls['success'];
    }
}
