<?php
abstract class Vpc_Form_Container_Abstract_Component extends Vpc_Form_Field_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['paragraphs'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Form_Dynamic_Paragraphs_Component'
        );
        return $ret;
    }

    public function hasContent()
    {
        return true;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paragraphs'] = $this->getData()->getChildComponent('-paragraphs');
        return $ret;
    }
}