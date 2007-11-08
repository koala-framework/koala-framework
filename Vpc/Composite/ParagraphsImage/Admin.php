<?php
class Vpc_Composite_ParagraphsImage_Admin extends Vpc_Admin
{
    public function getControllerClass()
    {
        return 'Vps.Component.TabPanel';
    }

    public function getControllerConfig($class, $pageId, $componentKey)
    {
        $pClass = Vpc_Abstract::getSetting($class, 'paragraphsClass');
        $conf = Vpc_Admin::getConfig($pClass, $pageId, $componentKey . '-1');
        $config['tabs']['Paragraphs'] = $conf; 
        
        $iClass = Vpc_Abstract::getSetting($class, 'imageClass');
        $conf = Vpc_Admin::getConfig($iClass, $pageId, $componentKey . '-2');
        $config['tabs']['Image'] = $conf; 
        
        $config['activeItem'] = 'Paragraphs';
        return $config;
    }

    public function setup()
    {
        Vpc_Admin::getInstance('Vpc_Paragraphs_Component')->setup();
        Vpc_Admin::getInstance('Vpc_Basic_Image_Component')->setup();
    }

    public function delete($component)
    {
        Vpc_Admin::getInstance($component->paragraphs)->delete($component->paragraphs);
        Vpc_Admin::getInstance($component->image)->delete($component->image);
    }
}
