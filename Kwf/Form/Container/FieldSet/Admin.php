<?php
class Vps_Form_Container_FieldSet_Admin extends Vpc_Formular_Dynamic_Admin
{
    public function getExtConfig()
    {
        return array_merge(parent::getExtConfig(), array(
            'xtype'=>'vps.form.container.fieldset',
            'controllerUrl' => $this->getControllerUrl()
        ));
    }
}
