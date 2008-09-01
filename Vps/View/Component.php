<?php
class Vps_View_Component extends Vps_View
{
    public function init()
    {
        parent::init();
        $this->addScriptPath('application/views');
    }

    public static function renderMasterComponent($component)
    {
        return self::renderComponent($component, false, true);
    }
    
    private static function _getComponent($componentId, $ignoreVisible)
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentById($componentId, array('ignoreVisible' => $ignoreVisible));
    }

    public static function renderComponent($component, $ignoreVisible = false, $isMaster = false, array $plugins = array())
    {
        if ($component instanceof Vps_Component_Data) {
            $componentClass = $component->componentClass;
            $componentId = $component->componentId;
        } else {
            $componentClass = $component['componentClass'];
            $componentId = $component['componentId'];
            unset($component);
        }
        
        // Falls es Cache gibt, Cache holen
        $cache = Vps_Component_Cache::getInstance();
        $cacheId = $cache->getCacheIdFromComponentId($componentId, $isMaster);
        static $cacheDisabled;
        if (is_null($cacheDisabled)) {
            $cacheDisabled = Zend_Registry::get('config')->debug->componentCache->disable;
        }

        if ($cacheDisabled || ($ret = $cache->load($componentClass, $cacheId))===false) {
            if (!isset($component)) {
                $component = self::_getComponent($componentId, $ignoreVisible);
            }
            if ($component) {
                if ($isMaster) {
                    $ret = Vps_View_Component::_renderMasterComponent($component);
                } else {
                    $ret = Vps_View_Component::_renderComponent($component);
                }
                $useCache = Vpc_Abstract::getSetting($component->componentClass, 'viewCache');
                if (!$cacheDisabled && ($useCache || $isMaster)) {
                    $cache->save($ret, $component->componentClass, $cacheId);
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
            if (!$p) {
                throw new Vps_Exception("Invalid Plugin specified '$p'");
            }
            $p = new $p($componentId);
            $ret = $p->processOutput($ret);
        }

        // content-Tags ersetzen
        preg_match_all("/{content: ([^ }]+) ([^ }]*)}(.*){content}/imsU", $ret, $matches);
        foreach ($matches[0] as $key => $search) {
            $componentId = $matches[2][$key];
            $componentClass = $matches[1][$key];
            $content = $matches[3][$key];
            
            if (!Zend_Registry::get('config')->debug->componentCache->disable) {
                $cacheId = $cache->getCacheIdFromComponentId($componentId, false, true);
                $cachedContent = $cache->load($componentClass, $cacheId);
                if ($cachedContent === false) {
                    $component = self::_getComponent($componentId, $ignoreVisible);
                    $cachedContent = $component->hasContent() ? $content : '';
                    $cache->save($cachedContent, $componentClass, $cacheId);
                }
            } else {
                $component = self::_getComponent($componentId, $ignoreVisible);
                $cachedContent = $component->hasContent() ? $content : '';
            }
            $ret = str_replace($search, $cachedContent, $ret);
        }
        
        // nocache-Tags ersetzen
        preg_match_all('/{nocache: ([^ }]+) ([^ }]*) ?([^}]*)}/', $ret, $matches);
        foreach ($matches[0] as $key => $search) {
            if ($matches[3][$key]) {
                $plugins = explode(' ', $matches[3][$key]);
            } else {
                $plugins = array();
            }
            $c = array(
                'componentClass' => $matches[1][$key],
                'componentId' => $matches[2][$key]
            );
            $replace = self::renderComponent($c, $ignoreVisible, false, $plugins);
            $ret = str_replace($search, $replace, $ret);
        }

        return $ret;
    }

    private static function _renderComponent(Vps_Component_Data $componentData)
    {
        $templateVars = $componentData->getComponent()->getTemplateVars();
        $template = Vpc_Admin::getComponentFile($componentData->componentClass, 'Component', 'tpl');
        if (!$template) {
            throw new Vps_Exception("No Template found for '$componentData->componentClass'");
        }

        if (is_null($templateVars)) {
            throw new Vps_Exception('getTemplateVars einer Komponenten gibt null zurück. return $vars; vergessen?');
        }

        return self::_render($template, $templateVars);

    }
    private static function _render($template, $templateVars)
    {
        $view = new Vps_View_Component();
        $view->assign($templateVars);
        return $view->render($template);
    }

    private static function _renderMasterComponent(Vps_Component_Data $componentData)
    {
        $templateVars = array();
        $templateVars['component'] = $componentData;
        foreach ($componentData->getChildBoxes() as $box) {
            if (!isset($templateVars['boxes'][$box->box]) || 
                $box->priority > $templateVars['boxes'][$box->box]->priority)
            {
                $templateVars['boxes'][$box->box] = $box;
            }
        }
        return self::_render('application/views/master/default.tpl', $templateVars);
    }



    /**
     * Finds a view script from the available directories.
     *
     * @param $name string The base name of the script.
     * @return void
     */
    protected function _script($name)
    {
        return $name;
    }
}
