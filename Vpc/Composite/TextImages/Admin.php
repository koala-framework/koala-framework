<?php
class Vpc_Composite_TextImages_Admin extends Vpc_Admin
{
    public function getControllerClass()
    {
        return 'Vps.Component.TabPanel';
    }

    public function getControllerConfig($class, $pageId, $componentKey)
    {
        $cls = Vpc_Abstract::getSetting($class, 'textClass');
        $conf = Vpc_Admin::getConfig($cls, $pageId, $componentKey . '-1');
        $config['tabs']['Text'] = $conf; 
        
        $cls = Vpc_Abstract::getSetting($class, 'imagesClass');
        $conf = Vpc_Admin::getConfig($cls, $pageId, $componentKey . '-2');
        $config['tabs']['Images'] = $conf; 
        
        $conf = Vpc_Admin::createConfig(
            'Vps.Auto.FormPanel', 
            $this->getControllerUrl($class), 
            array(), 
            $pageId, 
            $componentKey
        );
        $config['tabs']['Properties'] = $conf; 

        $config['activeItem'] = 'Text';
        return $config;
    }

    public function setup()
    {
        $cls = Vpc_Abstract::getSetting($class, 'textClass');
        Vpc_Admin::getInstance($cls)->setup();
        $cls = Vpc_Abstract::getSetting($class, 'imagesClass');
        Vpc_Admin::getInstance($cls)->setup();

        $fields['image_position'] = "enum('left', 'right', 'alternate') default NULL";
        $fields['enlarge'] = 'tinyint(3) NOT NULL';
        $this->createFormTable('vpc_composite_textimage', $fields);
    }

    public function delete($class, $pageId, $componentKey)
    {
        $cls = Vpc_Abstract::getSetting($class, 'textClass');
        Vpc_Admin::getInstance($cls)->delete($cls, $pageId, $componentKey . '-1');
        
        $cls = Vpc_Abstract::getSetting($class, 'imagesClass');
        Vpc_Admin::getInstance($cls)->delete($cls, $pageId, $componentKey . '-2');
        
        parent::delete($class, $pageId, $componentKey);
    }
}
