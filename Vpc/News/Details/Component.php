<?php
class Vpc_News_Details_Component extends Vpc_Paragraphs_Component implements Vpc_News_Interface_Component
{
    public $row;

    public static function getSettings()
    {
        $settings = parent::getSettings();
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
