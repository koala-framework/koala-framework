<?php
class Vps_View extends Zend_View
{
    public function init()
    {
        // je weiter unten, desto wichtiger ist der pfad
        $this->addScriptPath(VPS_PATH); // für tests, damit man eigene templates wo ablegen kann für Vps_Mail_Template ohne komponente
        $this->addScriptPath('');
        $this->addScriptPath(VPS_PATH . '/views');
        $this->addScriptPath('application/views');
        $this->addHelperPath(VPS_PATH . '/Vps/View/Helper', 'Vps_View_Helper');
    }
}