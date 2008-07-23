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

    public function getTemplateVars()
    {
        return array();
    }
}
