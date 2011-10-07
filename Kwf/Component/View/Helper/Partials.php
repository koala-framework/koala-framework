<?php
class Kwf_Component_View_Helper_Partials extends Kwf_Component_View_Renderer
{
    public function partials($component, $params = array())
    {
        if (!$component instanceof Kwf_Component_Data ||
            !method_exists($component->getComponent(), 'getPartialVars')
        ) throw new Kwf_Exception('Component has to implement Kwf_Component_Partial_Interface');
        $partialClass = $component->getComponent()->getPartialClass();
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
            $type = 'partial';
            $renderer = $this->_getRenderer();
            if ($renderer instanceof Kwf_Component_Renderer_Mail) {
                $type = 'mailPartial';
                $config['type'] = $renderer->getRenderFormat();
            }
            $ret .= $this->_getRenderPlaceholder($componentId, $config, $id, $type);
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
