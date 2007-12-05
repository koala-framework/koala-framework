<?php
class Vpc_Composite_TextImages_Admin extends Vpc_Admin
{
    public function getControllerClass()
    {
        return 'Vps.Component.TabPanel';
    }

    public function getControllerConfig($pageId, $componentKey)
    {
        $cls = Vpc_Abstract::getSetting($this->_class, 'textClass');
        $conf = Vpc_Admin::getConfig($cls, $pageId, $componentKey . '-text');
        $config['tabs']['Text'] = $conf; 
        
        $cls = Vpc_Abstract::getSetting($this->_class, 'imagesClass');
        $conf = Vpc_Admin::getConfig($cls, $pageId, $componentKey . '-images');
        $config['tabs']['Images'] = $conf; 
        
        $conf = Vpc_Admin::createConfig('Vps.Auto.FormPanel',
                                        $this->getControllerUrl(),
                                        array(), 
                                        $pageId, 
                                        $componentKey);
        $config['tabs']['Properties'] = $conf; 

        $config['activeItem'] = 'Text';
        return $config;
    }

    public function setup()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        Vpc_Admin::getInstance($classes['text'])->setup();
        Vpc_Admin::getInstance($classes['images'])->setup();

        $fields['image_position'] = "enum('left', 'right', 'alternate') default NULL";
        $this->createFormTable('vpc_composite_textimage', $fields);
    }

    public function delete($pageId, $componentKey)
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        Vpc_Admin::getInstance($classes['text'])->delete($pageId, $componentKey . '-text');
        Vpc_Admin::getInstance($classes['images'])->delete($pageId, $componentKey . '-images');

        parent::delete($pageId, $componentKey);
    }
}
