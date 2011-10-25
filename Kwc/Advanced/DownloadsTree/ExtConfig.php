<?php
class Kwc_Advanced_DownloadsTree_ExtConfig extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $config = $this->_getStandardConfig('kwc.advanced.downloadstree');
        $config['projectsUrl'] = Kwc_Admin::getInstance($this->_class)->getControllerUrl('Projects');
        $config['projectUrl'] = Kwc_Admin::getInstance($this->_class)->getControllerUrl('Project');
        $config['downloadsUrl'] = Kwc_Admin::getInstance($this->_class)->getControllerUrl('Downloads');
        return array(
            'customerarea' => $config
        );
    }
}
