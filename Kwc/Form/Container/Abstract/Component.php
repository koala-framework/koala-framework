<?php
abstract class Kwc_Form_Container_Abstract_Component extends Kwc_Form_Field_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['paragraphs'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Form_Dynamic_Paragraphs_Component'
        );
        $ret['editComponents'] = array('paragraphs');
        return $ret;
    }

    public function hasContent()
    {
        return true;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['paragraphs'] = $this->getData()->getChildComponent('-paragraphs');
        return $ret;
    }
}