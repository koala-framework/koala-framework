<?php
abstract class Vpc_Master_Abstract extends Vps_Component_Abstract
{
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

    public function getComponentId()
    {
        return $this->getTreeCacheRow()->component_id;
    }

    public function getDao()
    {
        return $this->getTreeCacheRow()->getTable()->getDao();
    }

    /**
     * Gibt die Variablen für View zurück.
     *
     * @return array Template-Variablen
     */
    public function getTemplateVars()
    {
        $vars = array();
        $vars['placeholder'] = $this->_getSetting('placeholder');

        $cssClass = array();
        $dirs = explode(PATH_SEPARATOR, get_include_path());
        $c = get_class($this);
        do {
            $file = str_replace('_', '/', $c);
            if (substr($file, -10) != '/Component') {
                $file .= '/Component';
            }
            $file .= '.css';
            foreach ($dirs as $dir) {
                if (is_file($dir . '/' . $file)) {
                    $cls = $c;
                    if (substr($cls, -10) == '_Component') {
                        $cls = substr($cls, 0, -10);
                    }
                    $cls = str_replace('_', '', $cls);
                    $cls = strtolower(substr($cls, 0, 1)) . substr($cls, 1);
                    $cssClass[] = $cls;
                    break;
                }
            }
        } while($c = get_parent_class($c));
        $vars['cssClass'] = implode(' ', array_reverse($cssClass));
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
        return $this->getTreeCacheRow()->tree_url;
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