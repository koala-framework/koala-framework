<?php
class Vps_Component_View_Helper_Partial extends Vps_Component_View_Renderer
{
    public function render($componentId, $config)
    {
        $component = $this->_getComponentById($componentId);
        $partialsClass = $config['class'];
        $partial = new $partialsClass($config['params']);

        // Normaler Output
        $componentClass = $component->componentClass;
        $template = $this->_getTemplate($componentClass, $config);
        if (!$template) {
            throw new Vps_Exception("No Partial-Template found for '$componentClass'");
        }
        $vars = $component->getComponent()->getPartialVars($partial, $config['id'], $config['info']);
        if (is_null($vars)) {
            throw new Vps_Exception('Return value of getPartialVars() returns null. Maybe forgot "return $ret?"');
        }
        $vars['info'] = $config['info'];
        $vars['data'] = $component;
        $view = new Vps_Component_View($this->_getRenderer());
        $view->assign($vars);
        return $view->render($template);
    }

    
    public function renderCached($cachedContent, $componentId, $config)
    {
        //add info to dynamic config, has to be done here because config is dynamic (from partials)

                                      //componentId   config
        preg_match_all('/{cc dynamic: [a-zA-Z0-9_-]+ ([^}]*)}/i', $cachedContent, $matches);
        foreach ($matches[1] as $match) {
            $dynamicConfig = $match;
            $dynamicConfig = $dynamicConfig != '' ? unserialize(base64_decode($dynamicConfig)) : array();
            $dynamicConfig['info'] = $config['info'];
            $dynamicConfig = base64_encode(serialize($dynamicConfig));
            $cachedContent = str_replace($match, $dynamicConfig, $cachedContent);
        }
        return $cachedContent;
    }


    protected function _getTemplate($componentClass, $config)
    {
        return Vpc_Abstract::getTemplateFile($componentClass, 'Partial');
    }
}
