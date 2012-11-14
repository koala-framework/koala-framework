<?php
class Kwf_View extends Zend_View
{
    public function init()
    {
        // je weiter unten, desto wichtiger ist der pfad
        $this->addScriptPath(KWF_PATH); // für tests, damit man eigene templates wo ablegen kann für Kwf_Mail_Template ohne komponente
        $this->addScriptPath('.');
        $this->addScriptPath('');
        $this->addScriptPath(KWF_PATH . '/views');
        if (defined('VKWF_PATH')) { //HACK
            $this->addScriptPath(VKWF_PATH . '/views');
        }
        $this->addScriptPath('views');
        $this->addHelperPath(KWF_PATH . '/Kwf/View/Helper', 'Kwf_View_Helper');
    }
}