<?php
class Vpc_Advanced_DownloadsTree_Admin extends Vpc_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        $this->_addResourcesBySameClass($acl);
    }

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['customerarea'] = $ret['form'];
        unset($ret['form']);
        $ret['customerarea']['xtype'] = 'vpc.advanced.downloadstree';
        $ret['customerarea']['projectsUrl'] = Vpc_Admin::getInstance($this->_class)->getControllerUrl('Projects');
        $ret['customerarea']['projectUrl'] = Vpc_Admin::getInstance($this->_class)->getControllerUrl('Project');
        $ret['customerarea']['downloadsUrl'] = Vpc_Admin::getInstance($this->_class)->getControllerUrl('Downloads');
        return $ret;
    }
}
