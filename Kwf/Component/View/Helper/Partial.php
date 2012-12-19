<?php
class Kwf_Component_View_Helper_Partial extends Kwf_Component_View_Renderer
{
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
        $view = new Kwf_Component_View($this->_getRenderer());
        $view->assign($vars);
        return $view->render($this->_getRenderer()->getTemplate($component, 'Partial'));
    }


    public function renderCached($cachedContent, $componentId, $config)
    {
        //add info to dynamic config, has to be done here because config is dynamic (from partials)

                                      //componentId   config
        preg_match_all('/{cc dynamic: [a-zA-Z0-9_-]+ ([^}]*)}/i', $cachedContent, $matches);
        foreach ($matches[1] as $match) {
            $dynamicConfig = $match;
            $dynamicConfig = $dynamicConfig != '' ? unserialize(base64_decode($dynamicConfig)) : array();
            if (!is_array($dynamicConfig['info'])) {
                $dynamicConfig['info'] = array();
            }
            $dynamicConfig['info'] = array_merge($config['info'], $dynamicConfig['info']);
            $dynamicConfig = base64_encode(serialize($dynamicConfig));
            $cachedContent = str_replace($match, $dynamicConfig, $cachedContent);
        }
        return $cachedContent;
    }

}
