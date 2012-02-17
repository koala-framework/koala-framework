<?php
class Kwf_Component_View_Helper_Partials extends Kwf_Component_View_Renderer
{
    public function partials($component, $params = array())
    {
        if (!$component instanceof Kwf_Component_Data ||
            !method_exists($component->getComponent(), 'getPartialVars')
        ) throw new Kwf_Exception('Component has to implement Kwf_Component_Partial_Interface');
        $componentClass = $component->componentClass;
        $partialClass = call_user_func(array($componentClass, 'getPartialClass'), $componentClass);
        if (method_exists($component->getComponent(), 'getPartialParams')) {
            $params = array_merge($component->getComponent()->getPartialParams(), $params);
        }
        $config = array(
            'class' => $partialClass,
            'params' => $params
        );
        return $this->_getRenderPlaceholder($component->componentId, $config);
    }

    public function render($componentId, $config)
    {
        $partialClass = $config['class'];
        $params = $config['params'];
        $partial = new $partialClass($params);
        $ids = $partial->getIds();
        $number = 0; $count = count($ids);
        $ret = '';
        foreach ($ids as $id) {
            $config = array(
                'id' => $id,
                'class' => $partialClass,
                'params' => $params,
                'info' => array(
                    'total' => $count,
                    'number' => $number++,
                )
            );
            $ret .= $this->_getRenderPlaceholder($componentId, $config, $id, 'partial');
        }
        if (empty($ids) && isset($params['noEntriesFound']) && $params['noEntriesFound']) {
            $ret .= '<span class="noEntriesFound">' . $params['noEntriesFound'] . '<span>';
        }
        return $ret;
    }

    public function enableCache()
    {
        return false;
    }
}
