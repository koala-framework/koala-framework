<?php
class Kwf_Component_View_Helper_BemClass extends Kwf_Component_View_Helper_Abstract
{
    public function bemClass($class)
    {
        $bemClasses = $this->_getView()->bemClasses;
        if ($bemClasses === false) return $class;
        $ret = array();
        foreach ($bemClasses as $i) {
            $ret[] = $i.$class;
        }
        return implode(' ', $ret);
    }
}
