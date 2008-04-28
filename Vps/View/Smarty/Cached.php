<?php
class Vps_View_Smarty_Cached extends Vps_View_Smarty
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->_smarty->caching = true;
        $this->_smarty->cache_dir = 'application/cache/smarty';
        $this->_smarty->cache_lifetime = 3600;
        $this->_smarty->register_function('component', array($this, 'component'), false, array('component'));
    }
    
    public function getSmarty()
    {
        return $this->_smarty;
    }

    public function component($params)
    {
        $componentId = $params['component'];
        $template = VPS_PATH . '/views/site.html';
        $view = new Vps_View_Smarty_Cached();
        if (is_array($componentId)) { return 'foo'; }
        if (!$view->isCached($template, $componentId)) {
            $tc = Vps_Dao::getTable('Vps_Dao_TreeCache');
            $where = array('component_id = ?' => $componentId);
            $row = $tc->fetchRow($where);
            if ($row) {
                $templateVars = $row->getComponent(false)->getTemplateVars();
                $view->component = $templateVars;
                $view->template = $templateVars['template'];
            } else {
                return 'bar';
            }
        }
        return $view->fetch($template, $componentId);
    }
    
    public function isCached($template, $cacheId, $compileId = null) {
        return $this->_smarty->is_cached($template, $cacheId, $compileId);
    }
}
