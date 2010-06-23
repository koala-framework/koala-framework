<?php
class Vps_Component_Output_Dynamic
{
    public function render($component, $config)
    {
        $class = $config[0];
        $args = unserialize(base64_decode($config[1]));

        $dynamic = new $class();
        call_user_func_array(array($dynamic, 'setArguments'), $args);
        $dynamic->setInfo(unserialize(base64_decode($config[2])));
        return $dynamic->getContent();
    }

    public static function getHelperOutput($component, $class, $args, $info = null)
    {
        $dynamicClass = Vps_Component_Abstract_Admin::getComponentClass($component->componentClass, $class);
        if (!class_exists($dynamicClass)) $dynamicClass = 'Vps_Component_Dynamic_' . $class;
        if (!class_exists($dynamicClass)) throw new Vps_Exception("Dynamic Class not found: $dynamicClass");
        $serializedArgs = base64_encode(serialize($args));
        $componentId = $component->componentId;
        $info = base64_encode(serialize($info));
        return "{dynamic: $componentId $dynamicClass $serializedArgs, $info}";
    }
}
