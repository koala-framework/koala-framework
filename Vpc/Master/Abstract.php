<?php
abstract class Vpc_Master_Abstract extends Vps_Component_Abstract {
    
    private $_treeCacheRow;

    public function __construct(Vps_Dao_Row_TreeCache $treeCacheRow)
    {
        $this->_treeCacheRow = $treeCacheRow;
        parent::__construct();
    }
    
    public function getTreeCacheRow()
    {
        return $this->_treeCacheRow;
    }

    public function getDbId()
    {
        return $this->getTreeCacheRow()->db_id;
    }

    public function getDao()
    {
        return $this->getTreeCacheRow()->getTable()->getDao();
    }

    /**
     * Gibt die Variablen für View zurück.
     *
     * Variable 'template' muss immer gesetzt werden.
     *
     * @return array Template-Variablen
     */
    public function getTemplateVars()
    {
        $vars = array();
        $vars['template'] = Vpc_Admin::getComponentFile(get_class($this), '', 'tpl');
        $vars['placeholder'] = $this->_getSetting('placeholder');
        return $vars;
    }

    protected function _getParam($param)
    {
        return isset($_REQUEST[$param]) ? $_REQUEST[$param] : null;
    }

    /**
     * Shortcut, fragt vom Seitenbaum die Url für eine Komponente ab
     *
     * @return string URL der Seite
     */
    public function getUrl()
    {
        return $this->getTreeCacheRow()->url;
    }

    public function getName()
    {
        return $this->getTreeCacheRow()->name;
    }

    /**
     * Shortcut, fragt vom Seitenbaum, ob die unsichtbaren Einträge
     * auch angezeige werden
     *
     * @return boolean
     */
    protected function _showInvisible()
    {
        return $this->getTreeCacheRow()->getTable()->showInvisible();
    }

    /**
     * @deprecated
     */
    protected function showInvisible()
    {
        return $this->_showInvisible();
    }

    public function getTable($tablename = null)
    {
        if (!$tablename) {
            $tablename = $this->_getSetting('tablename');
            if (!$tablename) {
                return null;
            }
        }
        if (!isset($this->_tables[$tablename])) {
            $this->_tables[$tablename] = new $tablename(array('componentClass'=>get_class($this)));
        }
        return $this->_tables[$tablename];
    }
}
?>