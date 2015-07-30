<?php
class Kwf_Component_View_Helper_BemClass extends Kwf_Component_View_Helper_Abstract
{
    public function bemClass($class, $nonBemFallback = null)
    {
        $bemClass = $this->_getView()->bemClass;
        if ($bemClass === false) {
            if ($nonBemFallback) return $nonBemFallback;
            return $class;
        }
        return $bemClass.$class;
    }
}
