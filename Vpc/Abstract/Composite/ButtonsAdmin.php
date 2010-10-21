<?php
class Vpc_Abstract_Composite_ButtonsAdmin extends Vpc_Abstract_Composite_Admin
{
    public function getExtConfig($type = self::EXT_CONFIG_DEFAULT)
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->_class, 'child');
        $ret = array();
        foreach ($classes as $id=>$cls) {
            $c = array_values(Vpc_Admin::getInstance($cls)->getExtConfig());
            foreach ($c as $k=>$i) {
                $i['componentIdSuffix'] = '-'.$id;
                $ret[] = $i;
            }
        }
        return $ret;
    }
}
