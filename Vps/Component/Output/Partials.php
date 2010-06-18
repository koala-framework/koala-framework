<?php
class Vps_Component_Output_Partials
{
    public function render($component, $config, $view)
    {
        $partialsClass = $config[0];
        $partial = new $partialsClass(unserialize(base64_decode(($config[1]))));
        $ids = $partial->getIds();
        $ret = '';
        $number = 0; $count = count($ids);
        foreach ($ids as $id) {
            $info = array(
                'total' => $count,
                'number' => $number++
            );
            $ret .= $this->_renderPartial($component, $partial, $id, $info, $view);
        }
        return $ret;
    }

    public function _renderPartial($component, $partial, $id, $info, $view)
    {
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
        $view->setParam('info', $info);
        //$view = new Vps_View();
        $view->assign($vars);
        return $view->render($template);
    }

    public static function getHelperOutput(Vps_Component_Data $component, $partialClass = null, $params = array())
    {
        if (!$component instanceof Vps_Component_Data ||
            !method_exists($component->getComponent(), 'getPartialVars')
        )
            throw new Vps_Exception('Component has to implement Vps_Component_Partial_Interface');
        if (!$partialClass) {
            if (!method_exists($component->getComponent(), 'getPartialClass')) {
                throw new Vps_Exception('If no partial class is given, component musst implement method "getPartialClass"');
            }
            $partialClass = $component->getComponent()->getPartialClass();
        }
        $componentId = $component->componentId;
        $componentClass = $component->componentClass;
        if (method_exists($component->getComponent(), 'getPartialParams')) {
            $params = array_merge($component->getComponent()->getPartialParams(), $params);
        }
        $serializedParams = base64_encode(serialize($params));
        return "{partials: $componentId $partialClass $serializedParams}";
    }
}
