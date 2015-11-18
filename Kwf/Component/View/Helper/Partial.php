<?php
class Kwf_Component_View_Helper_Partial extends Kwf_Component_View_Renderer
{
    public function partial($componentId, $config, $id, $viewCacheEnabled)
    {
        return $this->_getRenderPlaceholder($componentId, $config, $id, $viewCacheEnabled);
    }

    protected function _canBeIncludedInFullPageCache($componentId, $viewCacheEnabled)
    {
        return $viewCacheEnabled;
    }

    public function render($componentId, $config)
    {
        $component = $this->_getComponentById($componentId);
        $partialsClass = $config['class'];
        $partial = new $partialsClass($config['params']);

        // Normaler Output
        $vars = $component->getComponent()->getPartialVars($partial, $config['id'], $config['info']);
        if (is_null($vars)) {
            throw new Kwf_Exception('Return value of getPartialVars() returns null. Maybe forgot "return $ret?"');
        }
        $vars['info'] = $config['info'];
        $vars['data'] = $component;

        $renderer = $this->_getRenderer();
        if (isset($vars['template'])) {
            $tpl = $vars['template'];
        } else {
            $tpl = $renderer->getTemplate($component, 'Partial');
        }
        if (substr($tpl, -4) == '.tpl') {
            $view = new Kwf_Component_View($renderer);
            $view->assign($vars);
            $ret = $view->render($tpl);
        } else {
            $twig = new Kwf_Component_Renderer_Twig_Environment($renderer);
            $ret = $twig->render($tpl, $vars);
        }
        $ret = $this->_replaceKwfUp($ret);
        return $ret;
    }


    public function renderCached($cachedContent, $componentId, $config)
    {
        //add info to dynamic config, has to be done here because config is dynamic (from partials)
        $offset = 0;
        while (($start = strpos($cachedContent, '<kwc2 dynamic', $offset)) !== false) {
            $offset = $start+1;
            $end = strpos($cachedContent, '>', $start);

            $args = explode(' ', substr($cachedContent, $start+6, $end-$start-6));
            $dynamicConfig = $args[3];
            $dynamicConfig = $dynamicConfig != '' ? unserialize(base64_decode($dynamicConfig)) : array();
            $dynamicConfig['info'] = $config['info'];
            $args[3] = base64_encode(serialize($dynamicConfig));

            $newContent = '<kwc2 '.implode(' ', $args).'>';
            $cachedContent = substr($cachedContent, 0, $start).$newContent.substr($cachedContent, $end+1);
        }

        return $cachedContent;
    }

    public function getViewCacheSettings($componentId)
    {
        return $this->_getComponentById($componentId)->getComponent()->getViewCacheSettings();
    }
}
