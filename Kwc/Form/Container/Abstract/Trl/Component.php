<?php
class Kwc_Form_Container_Abstract_Trl_Component extends Kwc_Form_Field_Abstract_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['paragraphs'] = $this->getData()->getChildComponent('-paragraphs');
        return $ret;
    }

}

