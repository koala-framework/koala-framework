<?php
class Vpc_Forum_Directory_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        return array_merge(parent::getExtConfig(), array(
            'xtype'=>'vpc.forum',
            'controllerUrl' => $this->getControllerUrl()
        ));
    }
}