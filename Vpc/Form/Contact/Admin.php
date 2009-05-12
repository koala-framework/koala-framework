<?php
class Vpc_Formular_Contact_Admin extends Vpc_Formular_Dynamic_Admin
{
    public function getExtConfig()
    {
        return array_merge(parent::getExtConfig(), array(
            'xtype'=>'vpc.formular.contact',
            'controllerUrl' => $this->getControllerUrl()
        ));
    }
}
