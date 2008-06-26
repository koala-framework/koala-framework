<?php
class Vps_View_Component extends Vps_View
{
    public function init()
    {
        parent::init();
        $this->addScriptPath('application/views');
    }

    public static function renderCachedComponent($componentId, $isMaster = false)
    {
        if ($componentId instanceof Vps_Component_Data) {
            $component = $componentId;
            $componentId = $component->componentId;
        }
    
        // Falls es Cache gibt, Cache holen
        $cache = Vps_Component_Cache::getInstance();
        $cacheId = $cache->getCacheIdFromComponentId($componentId, $isMaster);
        $cacheDisabled = Zend_Registry::get('config')->debug->componentCache->disable;

        if ($cacheDisabled || ($return = $cache->load($cacheId))===false) {
            if (!isset($component)) {
                $component = Vps_Component_Data_Root::getInstance()->getComponentById($componentId);
            }

            if ($component) {
                $return = Vps_View_Component::_renderComponent($component, $isMaster);
                if (!$cacheDisabled) {
                    $tag = $isMaster ? 'master' : $component->componentClass;
                    $cache->save($return, $cacheId, array($tag));
                }
            } else {
                $return = "Component '$componentId' not found";
                //todo: throw error
            }
        }
        
        // nocache-Tags ersetzen
        preg_match_all('/{nocache: (.+?)}/', $return, $matches);
        foreach ($matches[0] as $key => $search) {
            $replace = self::renderCachedComponent($matches[1][$key]);
            $return = str_replace($search, $replace, $return);
        }

        return $return;
    }

    private static function _renderComponent(Vps_Component_Data $componentData, $isMaster = false)
    {
        $componentId = $componentData->componentId;

        if ($isMaster) {
            $templateVars = array();
            foreach (Zend_Registry::get('config')->vpc->masterComponents->toArray()
             as $componentClass)
            {
                $component = new $componentClass($componentData);
                if (!$component instanceof Vpc_Master_Abstract) {
                    throw new Vps_Exception('vpc.masterComponent has to be instance of Vpc_Master_Abstract');
                }
                $vars = $component->getTemplateVars();
                $templateVars = array_merge($templateVars, $vars);
            }
            $templateVars['component'] = $componentId;
            $template = 'application/views/master/default.tpl';
        } else {
            $templateVars = $componentData->getComponent()->getTemplateVars();
            $template = Vpc_Admin::getComponentFile($componentData->componentClass, 'Component', 'tpl');
            if (!$template) {
                throw new Vps_Exception("No Template found for '$componentData->componentClass'");
            }
        }

        if (is_null($templateVars)) {
            throw new Vps_Exception('getTemplateVars einer Komponenten gibt null zurück. return $vars; vergessen?');
        }

        $view = new Vps_View_Component();
        foreach ($templateVars as $key => $val) {
            $view->$key = $val;
        }
        return $view->render($template);
    }

    protected function _script($name)
    {
        return $name;
    }
}
