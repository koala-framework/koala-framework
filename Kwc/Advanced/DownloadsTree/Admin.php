<?php
class Kwc_Advanced_DownloadsTree_Admin extends Kwc_Admin
{
    public function addResources(Kwf_Acl $acl)
    {
        parent::addResources($acl);
        $this->_addResourcesBySameClass($acl);
    }

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['customerarea'] = $ret['form'];
        unset($ret['form']);
        $ret['customerarea']['xtype'] = 'kwc.advanced.downloadstree';
        $ret['customerarea']['projectsUrl'] = Kwc_Admin::getInstance($this->_class)->getControllerUrl('Projects');
        $ret['customerarea']['projectUrl'] = Kwc_Admin::getInstance($this->_class)->getControllerUrl('Project');
        $ret['customerarea']['downloadsUrl'] = Kwc_Admin::getInstance($this->_class)->getControllerUrl('Downloads');
        return $ret;
    }
}
