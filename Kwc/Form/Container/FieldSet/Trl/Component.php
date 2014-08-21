<?php
class Kwc_Form_Container_FieldSet_Trl_Component extends Kwc_Form_Container_Abstract_Trl_Component
{
    protected function _getFormField()
    {
        $ret = new Kwf_Form_Container_FieldSet();
        $ret->setTitle($this->getRow()->title);
        return $ret;
    }
}

