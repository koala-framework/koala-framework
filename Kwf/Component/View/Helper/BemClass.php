<?php
class Kwf_Component_View_Helper_BemClass extends Kwf_Component_View_Helper_Abstract
{
    public function bemClass($class)
    {
        $bemClass = $this->_getView()->bemClass;
        if ($bemClass === false) return $class;
        return $bemClass.$class;
    }
}
