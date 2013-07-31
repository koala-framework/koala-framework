<?php
class Kwf_Component_View_Helper_Partials extends Kwf_Component_View_Renderer
{
    public function partials($component, $params = array())
    {
        if (!$component instanceof Kwf_Component_Data ||
            !method_exists($component->getComponent(), 'getPartialVars')
        ) throw new Kwf_Exception('Component has to implement Kwf_Component_Partial_Interface');
        $componentClass = $component->componentClass;
        $c = strpos($componentClass, '.') ? substr($componentClass, 0, strpos($componentClass, '.')) : $componentClass;
        $partialClass = call_user_func(array($c, 'getPartialClass'), $componentClass);
        if (method_exists($component->getComponent(), 'getPartialParams')) {
            $params = array_merge($component->getComponent()->getPartialParams(), $params);
        }
        $viewCacheSettings = $component->getComponent()->getViewCacheSettings();
        $config = array(
            'class' => $partialClass,
            'params' => $params,
            'viewCacheEnabled' => $viewCacheSettings['enabled']
        );
        return $this->_getRenderPlaceholder($component->componentId, $config);
    }

    public function render($componentId, $partialsConfig)
    {
        $partialClass = $partialsConfig['class'];
        $params = $partialsConfig['params'];
        $partial = new $partialClass($params);
        $ids = $partial->getIds();
        $number = 0; $count = count($ids);
        $ret = '';
        $helper = new Kwf_Component_View_Helper_Partial();
        $helper->setRenderer($this->_getRenderer());
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

            $content = $helper->partial($componentId, $config, $id, $partialsConfig['viewCacheEnabled']);

            if (isset($params['tpl'])) {
                $tpl = $params['tpl'];
            } else {
                $tpl = '{content}';
            }
            $ret .= str_replace(array('{id}', '{content}'), array($id, $content), $tpl);
        }
        if (empty($ids) && isset($params['noEntriesFound']) && $params['noEntriesFound']) {
            $ret .= '<span class="noEntriesFound">' . $params['noEntriesFound'] . '</span>';
        }
        return $ret;
    }


    public function getViewCacheSettings($componentId)
    {
        $component = $this->_getComponentById($componentId);
        $componentClass = $component->componentClass;
        $c = strpos($componentClass, '.') ? substr($componentClass, 0, strpos($componentClass, '.')) : $componentClass;
        $partialClass = call_user_func(array($c, 'getPartialClass'), $componentClass);
        $useViewCache = call_user_func(array($partialClass, 'useViewCache'));
        return array(
            'enabled' => $useViewCache,
            'lifetime' => null
        );
    }
}
