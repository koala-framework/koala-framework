<?php
class Vps_View_Component extends Vps_View
{
    public function init()
    {
        parent::init();
        $this->addScriptPath('application/views');
    }

    public static function renderCachedComponent($componentClass, $componentId = null, $isMaster = false, $plugins = array())
    {
        if ($componentClass instanceof Vps_Component_Data) {
            $component = $componentClass;
            $componentClass = $component->componentClass;
            $componentId = $component->componentId;
        }

        // Falls es Cache gibt, Cache holen
        $cache = Vps_Component_Cache::getInstance();
        $cacheId = $cache->getCacheIdFromComponentId($componentId, $isMaster);
        $cacheDisabled = Zend_Registry::get('config')->debug->componentCache->disable;

        if ($cacheDisabled || ($ret = $cache->load($componentClass, $cacheId))===false) {
            if (!isset($component)) {
                $component = Vps_Component_Data_Root::getInstance()->getComponentById($componentId);
            }

            if ($component) {
                $ret = Vps_View_Component::_renderComponent($component, $isMaster);
                $useCache = Vpc_Abstract::getSetting($component->componentClass, 'viewCache');
                if (!$cacheDisabled && ($useCache || $isMaster)) {
                    $tags = array($isMaster ? 'master' : $component->componentClass);
                    $cache->save($ret, $component->componentClass, $cacheId, $tags);
                }
            } else {
                $ret = "Component '$componentId' not found";
                //todo: throw error
            }
            Vps_Benchmark::count('rendered nocache', $componentId);
        } else {
            Vps_Benchmark::count('rendered cache', $componentId);
        }

        //plugins _nach_ im cache speichern ausführen
        foreach ($plugins as $p) {
            $p = new $p($componentId);
            $ret = $p->processOutput($ret);
        }

        // nocache-Tags ersetzen
        preg_match_all('/{nocache: ([^ }]+) ?([^ }]*) ?([^}]*)}/', $ret, $matches);
        foreach ($matches[0] as $key => $search) {
            if ($matches[3][$key]) {
                $plugins = explode(' ', $matches[3][$key]);
            } else {
                $plugins = array();
            }
            $replace = self::renderCachedComponent($matches[1][$key], $matches[2][$key], false, $plugins);
            $ret = str_replace($search, $replace, $ret);
        }

        return $ret;
    }

    private static function _renderComponent(Vps_Component_Data $componentData, $isMaster)
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
            $templateVars['component'] = $componentData;
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
        $view->assign($templateVars);
        return $view->render($template);
    }

    protected function _script($name)
    {
        return $name;
    }
}
