<?php
class Vpc_Forum_Posts_Write_Success_Component extends Vpc_Formular_Success_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $t = $this->getParentComponent()->getThreadComponent();
        if ($t) {
            $ret['threadUrl'] = $t->getUrl();
        } else {
            $ret['threadUrl'] = '';
        }
        $ret['groupUrl'] = $this->getParentComponent()->getGroupComponent()->getUrl();
        $ret['forumUrl'] = $this->getParentComponent()->getForumComponent()->getUrl();
        return $ret;
    }
}
