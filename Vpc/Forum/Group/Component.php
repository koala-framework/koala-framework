<?php
class Vpc_Forum_Group_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'childComponentClasses' => array(
                'newthread'    => 'Vpc_Forum_NewThread_Component',
                'thread'       => 'Vpc_Forum_Thread_Component'
            ),
            'tablename'     => 'Vpc_Forum_Group_Model'
        ));
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $group = $this->getTable()->find($this->getCurrentPageKey())->current();

        $where = array();
        $where['component_id = ?'] = $this->getDbId();
        $ret['threads'] = array();

        $t = new Vpc_Forum_Thread_Model();
        foreach ($t->fetchAll($where) as $thread) {
            $page = $this->getPageFactory()->getChildPageByRow($thread);
            $ret['threads'][] = $page->getThreadVars();
        }

        $ret['newThreadUrl'] = $this->getPageFactory()
                                    ->getChildPageById('newthread')->getUrl();

        $ret['group'] = $this->getGroupComponent()->getName();
        $ret['groupUrl'] = $this->getGroupComponent()->getUrl();
        $ret['forum'] = $this->getForumComponent()->getName();
        $ret['forumUrl'] = $this->getForumComponent()->getUrl();
        return $ret;
    }
    public function getForumComponent()
    {
        return $this->getParentComponent();
    }
    public function getGroupComponent()
    {
        return $this;
    }
}
