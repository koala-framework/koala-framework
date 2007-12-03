<?php
class Vpc_News_Details_Component extends Vpc_Paragraphs_Component
{
    public $row;
    
    public static function getSettings()
    {
        $settings = parent::getSettings();
        foreach ($settings['childComponentClasses'] as $key => $class) {
            if (Vpc_Abstract::getSetting($class, 'hideInNews')) {
                unset($settings['childComponentClasses'][$key]);
            }
        }
        return $settings;
    }
    
    public function getTemplateVars()
    {
        $vars = parent::getTemplateVars();
        $vars['news'] = $this->row->toArray();
        return $vars;
    }
    
    public function setRow($row)
    {
        $this->row = $row;
    }
}
