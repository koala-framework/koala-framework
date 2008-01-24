<?php
class Vpc_News_Details_Component extends Vpc_Paragraphs_Component implements Vpc_News_Interface
{
    public $row;
    public $news = 'true'; // Wird benötigt um Endlosschleife bei getSettings zu vermeiden

    public static function getSettings()
    {
        $settings = parent::getSettings();
        foreach ($settings['childComponentClasses'] as $key => $class) {
            $vars = get_class_vars($class);
            if (!isset($vars['news']) &&
                Vpc_Abstract::getSetting($class, 'hideInNews'))
            {
                unset($settings['childComponentClasses'][$key]);
            }
        }
        return $settings;
    }

    public function getNewsComponent()
    {
        return $this->getParentComponent();
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
