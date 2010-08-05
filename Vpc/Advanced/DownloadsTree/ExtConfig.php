<?php
class Vpc_Advanced_DownloadsTree_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $config = $this->_getStandardConfig('vpc.advanced.downloadstree');
        $config['projectsUrl'] = Vpc_Admin::getInstance($this->_class)->getControllerUrl('Projects');
        $config['projectUrl'] = Vpc_Admin::getInstance($this->_class)->getControllerUrl('Project');
        $config['downloadsUrl'] = Vpc_Admin::getInstance($this->_class)->getControllerUrl('Downloads');
        return array(
            'customerarea' => $config
        );
    }
}
