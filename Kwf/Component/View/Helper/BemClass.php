<?php
class Kwf_Component_View_Helper_BemClass extends Kwf_Component_View_Helper_Abstract
{
    public function bemClass($class)
    {
        static $up;
        if (!isset($up)) $up = Kwf_Config::getValue('application.uniquePrefix');
        if (!$up) return $class;

        $classes = Kwc_Abstract::getSetting($this->_getView()->data->componentClass, 'processedCssClass');;
        $classes = explode(' ', $classes);
        $ret = array();
        foreach ($classes as $i) {
            $ret[] = $i.'__'.$class;
        }
        return implode(' ', $ret);
    }
}
