<?php
class Vps_View extends Zend_View
{
    protected $_masterTemplate = null;

    public function init()
    {
        $this->addScriptPath('');
        $this->addScriptPath(VPS_PATH . '/views');
        $this->addScriptPath('application/views');
        $this->addHelperPath(VPS_PATH . '/Vps/View/Helper', 'Vps_View_Helper');
    }

    public function render($name)
    {
        if (!is_null($this->_masterTemplate)) {
            //TODO: partial von Zend_View verwenden
            $this->renderedTemplate = parent::render($name);
            $name = $this->getMasterTemplate();
        }
        return parent::render($name);
    }

    public function setMasterTemplate($tpl)
    {
        $this->_masterTemplate = $tpl;
    }

    public function getMasterTemplate()
    {
        return $this->_masterTemplate;
    }

}
