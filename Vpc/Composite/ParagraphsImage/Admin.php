<?php
class Vpc_Composite_ParagraphsImage_Admin extends Vpc_Admin
{
    public function getControllerClass()
    {
        return 'Vps.Component.TabPanel';
    }

    public function getControllerConfig($componentId)
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');

        $conf = Vpc_Admin::getConfig($classes['paragraphs'], $componentId . '-paragraphs');
        $config['tabs']['Paragraphs'] = $conf; 

        $conf = Vpc_Admin::getConfig($classes['image'], $componentId . '-image');
        $config['tabs']['Image'] = $conf; 

        $config['activeItem'] = 'Paragraphs';
        return $config;
    }

    public function setup()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');

        Vpc_Admin::getInstance($classes['paragraphs'])->setup();
        Vpc_Admin::getInstance($classes['image'])->setup();
    }

    public function delete($class, $componentId)
    {
        $pClass = Vpc_Abstract::getSetting($this->_class, 'paragraphsClass');
        Vpc_Admin::getInstance($pClass)->delete($pClass, $componentId . '-paragraphs');
        
        $iClass = Vpc_Abstract::getSetting($this->_class, 'imageClass');
        Vpc_Admin::getInstance($iClass)->delete($iClass, $componentId . '-image');
    }
}
