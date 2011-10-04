<?php
class Vpc_Forum_Directory_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $config = $this->_getStandardConfig('vpc.forum');
        $config['moderatorsControllerUrl'] = $this->getControllerUrl('Moderators');
        $config['moderatorsToGroupControllerUrl'] = $this->getControllerUrl('ModeratorsToGroup');
        return array(
            'forum' => $config
        );
    }
}
