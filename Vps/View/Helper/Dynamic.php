<?php
class Vps_View_Helper_Dynamic extends Vps_View_Helper_Abstract
{
    public function dynamic($class)
    {
        $args = array_slice(func_get_args(), 1);
        $component = $this->_getView()->data;
        $info = $this->_getView()->info;
        $dynamicClass = Vps_Component_Abstract_Admin::getComponentClass($component->componentClass, $class);
        if (!class_exists($dynamicClass)) $dynamicClass = 'Vps_Component_Dynamic_' . $class;
        if (!class_exists($dynamicClass)) throw new Vps_Exception("Dynamic Class not found: $dynamicClass");
        $serializedArgs = base64_encode(serialize($args));
        $componentId = $component->componentId;
        $info = base64_encode(serialize($info));
        return "{dynamic: $componentId $dynamicClass $serializedArgs $info}";
    }
}
