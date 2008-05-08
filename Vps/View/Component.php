<?php
class Vps_View_Component extends Vps_View
{
    public function init()
    {
        parent::init();
        $this->addScriptPath('application/views');
    }
    
    public static function renderCachedComponent($id, $isMaster = false, $renderDirectly = false)
    {
        $cache = Vps_Component_Cache::getInstance();
        // Parameter ordnen, je nachdem von woher aufgerufen
        if (is_array($id)) { // Komponente wird ausgegeben
            $cacheId = $id[1];
            $componentId = $cache->getComponentIdFromCacheId($cacheId);
            $renderDirectly = true;
        } else { // Komponente wird gecacht
            $componentId = $id;
            $cacheId = $cache->getCacheIdFromComponentId($componentId);
            $renderDirectly = $renderDirectly || false;
        }
        if ($isMaster) {
            $cacheId = $cacheId . '__master';
        }
        $hasCache = $cache->getCache()->test($cacheId);
        if (!$hasCache) {
            $tc = Vps_Dao::getTable('Vps_Dao_TreeCache');
            $where = array('component_id = ?' => $componentId);
            $row = $tc->fetchRow($where);
            $html = Vps_View_Component::renderComponent($row, $isMaster);
            $tag = $isMaster ? 'master' : $row->component_class;
            $cache->getCache()->save($html, $cacheId, array($tag));
            $return = '{nocache: ' . $cacheId . '}';
        } else {
            $return = $cache->getCache()->load($cacheId);
        }
        
        if ($renderDirectly || $isMaster) {
            $return = preg_replace_callback(
                '/{nocache: (.+)}/', 
                array('Vps_View_Component', 'renderCachedComponent'), 
                $return
            );
        }
    
        return $return;
    }
    
    public static function renderComponent($row, $isMaster = false)
    {
        $componentId = $row->component_id;
        if ($isMaster) {
            $templateVars = array();
            foreach (Zend_Registry::get('config')->vpc->masterComponents->toArray()
             as $componentClass)
            {
                $component = new $componentClass($row);
                if (!$component instanceof Vpc_Master_Abstract) {
                    throw new Vps_Exception('vpc.masterComponent has to be instance of Vpc_Master_Abstract');
                }
                $templateVars += $component->getTemplateVars();
            }
            $templateVars['component'] = $componentId;
            $template = 'application/views/Master.tpl';
        } else {
            $templateVars = $row->getComponent(false)->getTemplateVars();
            $template = $templateVars['template'];
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
