<?php
class Vps_View_Component extends Vps_View
{
    public function init()
    {
        parent::init();
        $this->addScriptPath('application/views');
    }

    public static function renderMasterComponent($component, $masterTemplate = 'application/views/master/default.tpl')
    {
        return self::renderComponent($component, false, $masterTemplate);
    }
    
    private static function _getComponent($componentId, $ignoreVisible)
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentById($componentId, array('ignoreVisible' => $ignoreVisible));
    }

    public static function renderComponent($component, $ignoreVisible = false, $masterTemplate = false, array $plugins = array())
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
        $cacheId = $cache->getCacheIdFromComponentId($componentId, $masterTemplate);
        static $cacheDisabled;
        if (is_null($cacheDisabled)) {
            $cacheDisabled = Zend_Registry::get('config')->debug->componentCache->disable;
        }
        if ($cacheDisabled) $cacheId = false;
        if (!$masterTemplate && !Vpc_Abstract::getSetting($componentClass, 'viewCache')) {
            $cacheId = false;
        }

        static $loadedPageIds = array();
        static $componentCache = array();
        if ($masterTemplate) {
            $pageId = $component->getPage()->componentId;
            if (!in_array($pageId, $loadedPageIds)) {
                $loadedPageIds[] = $pageId;
                $pageId = $cache->getCacheIdFromComponentId($pageId);
                $sql = "SELECT id, content FROM cache_component WHERE page_id='$pageId'";
                $rows = Zend_Registry::get('db')->query($sql)->fetchAll();
                foreach ($rows as $row) {
                    $componentCache[$row['id']] = $row['content'];
                }
            }
        }


        if (isset($componentCache[$cacheId])) {
            $ret = $componentCache[$cacheId];
            Vps_Benchmark::count('rendered cache (preloaded)', $componentId.($masterTemplate?' (master)':''));
        } else if (!$cacheId || ($ret = $cache->load($cacheId))===false) {
            if (!isset($component)) {
                $component = self::_getComponent($componentId, $ignoreVisible);
            }
            if ($component) {
                if ($masterTemplate) {
                    $ret = Vps_View_Component::_renderMasterComponent($component, $masterTemplate);
                } else {
                    $ret = Vps_View_Component::_renderComponent($component);
                }
                if ($cacheId) {
                    $cache->save($ret, $cacheId, array(
                        'componentClass'=>$component->componentClass,
                        'pageId' => $cache->getCacheIdFromComponentId($component->getPage()->componentId),
                    ));
                }
            } else {
                $ret = "Component '$componentId' not found";
                //todo: throw error
            }
            Vps_Benchmark::count('rendered nocache', $componentId.($masterTemplate?' (master)':''));
        } else {
            Vps_Benchmark::count('rendered cache', $componentId.($masterTemplate?' (master)':''));
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

            $cachedContent = false;
            $cacheId = $cache->getCacheIdFromComponentId($componentId, false, true);
            if (!$cacheDisabled) {
                if (isset($componentCache[$cacheId])) {
                    $cachedContent = $cache->load($cacheId);
                } else {
                    $cachedContent = $cache->load($cacheId);
                }
            }
            if ($cachedContent === false) {
                $component = self::_getComponent($componentId, $ignoreVisible);
                if (!$component) {
                    throw new Vps_Exception("Can't find component '$componentId'");
                }
                $cachedContent = $component->hasContent() ? $content : '';
                if (!$cacheDisabled) {
                    $cache->save($cachedContent, $cacheId, array(
                        'componentClass'=>$componentClass,
                        'pageId' => $cache->getCacheIdFromComponentId($component->getPage()->componentId)
                    ));
                }
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

    private static function _renderMasterComponent(Vps_Component_Data $componentData, $masterTemplate)
    {
        $templateVars = array();
        $templateVars['component'] = $componentData;
        $templateVars['boxes'] = array();
        Vps_Debug::enable();
        foreach ($componentData->getChildBoxes() as $box) {
            $templateVars['boxes'][$box->box] = $box;
        }
        return self::_render($masterTemplate, $templateVars);
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
