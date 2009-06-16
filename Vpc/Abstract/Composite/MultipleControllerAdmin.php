<?php
class Vpc_Abstract_Composite_MultipleControllerAdmin extends Vpc_Abstract_Composite_Admin
{
    public function getExtConfig()
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->_class, 'child');

        $ret = array();
        foreach ($classes as $id=>$cls) {
            $c = Vpc_Admin::getInstance($cls)->getExtConfig();
            foreach ($c as $k=>$i) {
                $i['componentIdSuffix'] = '-'.$id;
                $ret[$id.'-'.$k] = $i;
            }
        }
        return $ret;
    }
}
