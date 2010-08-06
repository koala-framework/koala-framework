<?php
class Vps_Component_View_Helper_Partial extends Vps_Component_View_Renderer
{
    private $_currentId = '';

    public function render($component, $config, $view)
    {
        $partialsClass = $config[0];
        $partial = new $partialsClass(unserialize(base64_decode(($config[1]))));
        $id = $config[2];
        $info = unserialize(base64_decode(($config[3])));

        // Normaler Output
        $componentClass = $component->componentClass;
        $template = Vpc_Abstract::getTemplateFile($componentClass, 'Partial');
        if (!$template) {
            throw new Vps_Exception("No Partial-Template found for '$componentClass'");
        }
        $vars = $component->getComponent()->getPartialVars($partial, $id, $info);
        if (is_null($vars)) {
            throw new Vps_Exception('Return value of getPartialVars() returns null. Maybe forgot "return $ret?"');
        }
        $vars['info'] = $info;
        $vars['data'] = $component;
        $view->assign($vars);
        $this->_currentId = $id;
        return $view->render($template);
    }

    protected function _getCacheValue()
    {
        return $this->_currentId;
    }

    protected function _saveMeta($component)
    {
        return $component->getComponent()->savePartialCache($this->_getCacheValue());
    }
}
