<?php
class Vpc_Composite_TextImages_Admin extends Vpc_Admin
{
    public function getControllerClass()
    {
        return 'Vps.Component.TabPanel';
    }

    public function getControllerConfig($class, $pageId, $componentKey)
    {
        $cls = Vpc_Abstract::getSetting($class, 'imagesClass');
        $conf = Vpc_Admin::getConfig($cls, $pageId, $componentKey . '-1');
        $config['tabs']['Images'] = $conf; 
        
        $cls = Vpc_Abstract::getSetting($class, 'textClass');
        $conf = Vpc_Admin::getConfig($cls, $pageId, $componentKey . '-2');
        $config['tabs']['Text'] = $conf; 
        
        $conf = Vpc_Admin::createConfig(
            'Vps.Auto.FormPanel', 
            $this->getControllerUrl($class), 
            array(), 
            $pageId, 
            $componentKey
        );
        $config['tabs']['Properties'] = $conf; 

        $config['activeItem'] = 'Images';
        return $config;
    }

    public function setup()
    {
        $this->copyTemplate('Template.html', 'Composite/TextImages.html');

        Vpc_Admin::getInstance('Vpc_Basic_Html_Component')->setup();
        Vpc_Admin::getInstance('Vpc_Basic_Image_Component')->setup();

        $fields['image_position'] = "enum('left', 'right', 'alternate') default NULL";
        $fields['enlarge'] = 'tinyint(3) NOT NULL';
        $this->createTable('vpc_composite_textimage', $fields);
    }

    public function delete($component)
    {
        foreach ($component->getChildComponents() as $c) {
            Vpc_Admin::getInstance($c)->delete($c);
        }
    }
}
