<?php
class Vpc_Forum_Posts_Write_Component extends Vpc_Posts_Write_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['success'] = 'Vpc_Forum_Posts_Write_Success_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['thread'] = $this->getThreadComponent()->getName();
        $ret['threadUrl'] = $this->getThreadComponent()->getUrl();
        $ret['group'] = $this->getGroupComponent()->getName();
        $ret['groupUrl'] = $this->getGroupComponent()->getUrl();
        $ret['forum'] = $this->getForumComponent()->getName();
        $ret['forumUrl'] = $this->getForumComponent()->getUrl();
        return $ret;
    }

    public function getThreadComponent()
    {
        return $this->getParentComponent()->getParentComponent();
    }

    public function getGroupComponent()
    {
        return $this->getThreadComponent()->getGroupComponent();
    }

    public function getForumComponent()
    {
        return $this->getThreadComponent()->getForumComponent();
    }
}