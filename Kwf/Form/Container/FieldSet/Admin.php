<?php
class Kwf_Form_Container_FieldSet_Admin extends Kwc_Formular_Dynamic_Admin
{
    public function getExtConfig()
    {
        return array_merge(parent::getExtConfig(), array(
            'xtype'=>'kwf.form.container.fieldset',
            'controllerUrl' => $this->getControllerUrl()
        ));
    }
}
