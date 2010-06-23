<?php
class Vps_View_Helper_Dynamic extends Vps_View_Helper_Abstract
{
    public function dynamic($class)
    {
        $args = array_slice(func_get_args(), 1);
        return Vps_Component_Output_Dynamic::getHelperOutput($this->_getView()->data, $class, $args, $this->_getView()->info);
    }
}
