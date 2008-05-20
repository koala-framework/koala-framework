<?php
class Vpc_Forum_Posts_Component extends Vpc_Posts_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['write'] = 'Vpc_Forum_Posts_Write_Component';
        $ret['childComponentClasses']['post'] = 'Vpc_Forum_Posts_Post_Component';
        $ret['childComponentClasses']['observe'] = 'Vpc_Forum_Posts_Observe_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['observe'] = $this->getPageFactory()->getChildPageById('observe')->getComponent()->getTemplateVars();
        $ret['thread'] = $this->getName();
        $ret['threadUrl'] = $this->getUrl();
        $ret['group'] = $this->getGroupComponent()->getName();
        $ret['groupUrl'] = $this->getGroupComponent()->getUrl();
        $ret['forum'] = $this->getForumComponent()->getName();
        $ret['forumUrl'] = $this->getForumComponent()->getUrl();
        $ret['threadVars'] = $this->getParentComponent()->getThreadVars();
        return $ret;
    }

    public function getThreadComponent()
    {
        return $this->getParentComponent();
    }

    public function getGroupComponent()
    {
        return $this->getParentComponent()->getParentComponent();
    }

    public function getForumComponent()
    {
        return $this->getParentComponent()->getForumComponent();
    }
}
