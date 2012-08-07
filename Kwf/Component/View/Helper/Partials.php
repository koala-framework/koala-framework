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
        $config = array(
            'class' => $partialClass,
            'params' => $params
        );
        return $this->_getRenderPlaceholder($component->componentId, $config);
    }

    /**
     * Helper method not used in standard rendering process
     *
     * Used to render a single partial
     */
    public function singlePartial($componentId, $config, $id)
    {
        return $this->_getRenderPlaceholder($componentId, $config, $id, 'partial');
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
            $content = $this->_getRenderPlaceholder($componentId, $config, $id, 'partial');
            if (isset($params['tpl'])) {
                $tpl = $params['tpl'];
            } else {
                $tpl = '{content}';
            }
            $ret .= str_replace(array('{id}', '{content}'), array($id, $content), $tpl);
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
