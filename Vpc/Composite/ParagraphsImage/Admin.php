<?php
class Vpc_Composite_ParagraphsImage_Admin extends Vpc_Admin
{
    public function getControllerClass()
    {
        return 'Vps.Component.TabPanel';
    }

    public function getControllerConfig($pageId, $componentKey)
    {
        $pClass = Vpc_Abstract::getSetting($this->_class, 'paragraphsClass');
        $conf = Vpc_Admin::getConfig($pClass, $pageId, $componentKey . '-1');
        $config['tabs']['Paragraphs'] = $conf; 
        
        $iClass = Vpc_Abstract::getSetting($this->_class, 'imageClass');
        $conf = Vpc_Admin::getConfig($iClass, $pageId, $componentKey . '-2');
        $config['tabs']['Image'] = $conf; 
        
        $config['activeItem'] = 'Paragraphs';
        return $config;
    }

    public function setup()
    {
        $pClass = Vpc_Abstract::getSetting($this->_class, 'paragraphsClass');
        Vpc_Admin::getInstance($pClass)->setup();
        
        $iClass = Vpc_Abstract::getSetting($this->_class, 'imageClass');
        Vpc_Admin::getInstance($iClass)->setup();
    }

    public function delete($class, $pageId, $componentKey)
    {
        $pClass = Vpc_Abstract::getSetting($this->_class, 'paragraphsClass');
        Vpc_Admin::getInstance($pClass)->delete($pClass, $pageId, $componentKey . '-1');
        
        $iClass = Vpc_Abstract::getSetting($this->_class, 'imageClass');
        Vpc_Admin::getInstance($iClass)->delete($iClass, $pageId, $componentKey . '-2');
    }
}
