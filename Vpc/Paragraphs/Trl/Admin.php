<?php
class Vpc_Paragraphs_Trl_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        return array(
            'paragraphs' => array(
                'xtype'=>'vpc.paragraphs',
                'controllerUrl' => $this->getControllerUrl(),
                'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
                'icon' => $this->_getSetting('componentIcon')->__toString(),
                'previewWidth' => $this->_getSetting('previewWidth'),
                'showDelete' => false,
                'showPosition' => false,
            )
        );
    }
}
