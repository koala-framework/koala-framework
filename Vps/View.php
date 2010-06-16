<?php
class Vps_View extends Zend_View
{
    protected $_masterTemplate = null;

    public function init()
    {
        // je weiter unten, desto wichtiger ist der pfad
        $this->addScriptPath(VPS_PATH); // für tests, damit man eigene templates wo ablegen kann für Vps_Mail_Template ohne komponente
        $this->addScriptPath('');
        $this->addScriptPath(VPS_PATH . '/views');
        $this->addScriptPath('application/views');
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
