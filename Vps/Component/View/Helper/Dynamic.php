<?php
class Vps_Component_View_Helper_Dynamic extends Vps_Component_View_Renderer
{
    public function dynamic($class)
    {
        $component = $this->_getView()->data;
        $dynamicClass = Vps_Component_Abstract_Admin::getComponentClass($component->componentClass, $class);
        if (!class_exists($dynamicClass))
            $dynamicClass = 'Vps_Component_Dynamic_' . $class;
        if (!class_exists($dynamicClass))
            throw new Vps_Exception("Dynamic Class not found: $dynamicClass");
        $config = array(
            'class' => $dynamicClass,
            'arguments' => array_slice(func_get_args(), 1)
        );
        return $this->_getRenderPlaceholder($component->componentId, $config);
    }

    public function render($componentId, $config)
    {
        $class = $config['class'];
        $dynamic = new $class();
        call_user_func_array(array($dynamic, 'setArguments'), $config['arguments']);
        if (isset($config['info'])) $dynamic->setInfo($config['info']); //added to config in Partial::renderCached
        return $dynamic->getContent();
    }

    public function enableCache()
    {
        return false;
    }
}
