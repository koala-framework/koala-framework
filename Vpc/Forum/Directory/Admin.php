<?php
class Vpc_Forum_Directory_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        return array(
            'forum' => array(
                'xtype'=>'vpc.forum',
                'controllerUrl' => $this->getControllerUrl(),
                'moderatorsControllerUrl' => $this->getControllerUrl('Moderators'),
                'moderatorsToGroupControllerUrl' => $this->getControllerUrl('ModeratorsToGroup'),
                'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
                'icon' => $this->_getSetting('componentIcon')->__toString(),
            )
        );
    }
}