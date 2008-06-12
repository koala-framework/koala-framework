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
        // Falls es Cache gibt, Cache holen
        $cache = Vps_Component_Cache::getInstance();
        $cacheId = $cache->getCacheIdFromComponentId($componentId, $isMaster);
        $cacheDisabled = Zend_Registry::get('config')->debug->componentCache->disable;
        
        if ($cacheDisabled || ($return = $cache->load($cacheId))===false) {
            $tc = Vps_Dao::getTable('Vps_Dao_TreeCache');
            $where = array('component_id = ?' => $componentId);
            $row = $tc->fetchRow($where);
            if ($row) {
                $return = Vps_View_Component::_renderComponent($row, $isMaster);
                $tag = $isMaster ? 'master' : $row->component_class;
                if (!$cacheDisabled) {
                    $cache->save($return, $cacheId, array($tag));
                }
            } else {
                $return = "Component '$componentId' does not exist in TreeCache";
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

    private static function _renderComponent(Vps_Dao_Row_TreeCache $row, $isMaster = false)
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
            $template = 'application/views/master/default.tpl';
        } else {
            $templateVars = $row->getComponent()->getTemplateVars();
            $template = Vpc_Admin::getComponentFile($row->component_class, 'Component', 'tpl');
            if (!$template) {
                throw new Vps_Exception("No Template found for '$row->component_class'");
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
