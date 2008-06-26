<?php
abstract class Vpc_Master_Abstract extends Vps_Component_Abstract
{
    private $_data;

    public function __construct(Vps_Component_Data $data)
    {
        $this->_data = $data;
        parent::__construct();
    }
    
    public function getData()
    {
        return $this->_data;
    }

    public function getDbId()
    {
        return $this->getData()->dbId;
    }

    public function getComponentId()
    {
        return $this->getData()->componentId;
    }

    /**
     * Gibt die Variablen für View zurück.
     *
     * @return array Template-Variablen
     */
    public function getTemplateVars()
    {
        $ret = array();
        $ret['placeholder'] = $this->_getSetting('placeholder');

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
        $ret['cssClass'] = implode(' ', array_reverse($cssClass));
        if (Vpc_Abstract::hasSetting(get_class($this), 'cssClass')) {
            $ret['cssClass'] .= ' '.Vpc_Abstract::getSetting(get_class($this), 'cssClass');
        }
        return $ret;
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
        return $this->getData()->getPage()->url;
    }

    public function getName()
    {
        return $this->getData()->getPage()->name;
    }

    /**
     * Shortcut, fragt vom Seitenbaum, ob die unsichtbaren Einträge
     * auch angezeige werden
     *
     * @return boolean
     */
    protected function _showInvisible()
    {
        return Vps_Registry::get('config')->showInvisible;
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