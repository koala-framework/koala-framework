<?php
class Vpc_Basic_Image_Enlarge_TreeCache extends Vpc_Abstract_Composite_TreeCache
{
    protected function _init()
    {
        parent::_init();
        $cls = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        $this->_classes[1] = $cls['smallImage'];
        foreach ($cls as $id=>$class) {
            if ($class) $this->_classes[$id] = $class;
        }
    }
}
