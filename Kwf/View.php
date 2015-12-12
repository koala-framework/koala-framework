<?php
class Kwf_View extends Zend_View
{
    public function init()
    {
        // je weiter unten, desto wichtiger ist der pfad
        $this->addScriptPath(KWF_PATH); // für tests, damit man eigene templates wo ablegen kann für Kwf_Mail_Template ohne komponente
        $this->addScriptPath('.');
        $this->addScriptPath(KWF_PATH . '/views');
        if (defined('VKWF_PATH')) { //HACK
            $this->addScriptPath(VKWF_PATH . '/views');
        }
        $this->addScriptPath('views');
        $this->addHelperPath(KWF_PATH . '/Kwf/View/Helper', 'Kwf_View_Helper');
    }

    protected function _script($name)
    {
        //support absolute path
        if (file_exists($name)) {
            return $name;
        }
        return parent::_script($name);
    }

    protected static function _replaceKwfUp($ret)
    {
        static $up;
        if (!isset($up)) {
            $up = Kwf_Config::getValue('application.uniquePrefix');
            if ($up) $up .= '-';
        }
        return str_replace('kwfUp-', $up, $ret);
    }
}