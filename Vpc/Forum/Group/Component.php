<?php
class Vpc_Forum_Group_Component extends Vpc_Abstract
{
    private $_paging;

    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'childComponentClasses' => array(
                'newthread'    => 'Vpc_Forum_NewThread_Component',
                'thread'       => 'Vpc_Forum_Thread_Component',
                'paging'       => 'Vpc_Forum_Group_Paging_Component'
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
        $limit = $this->_getPagingComponent()->getLimit();
        $threads = $t->fetchAll($where, null, $limit['limit'], $limit['start']);
        foreach ($threads as $thread) {
            $page = $this->getPageFactory()->getChildPageByRow($thread);
            $ret['threads'][] = $page->getThreadVars();
        }
        $ret['paging'] = $this->_getPagingComponent()->getTemplateVars();

        $ret['newThreadUrl'] = $this->getPageFactory()
                                    ->getChildPageById('newthread')->getUrl();

        $ret['group'] = $this->getGroupComponent()->getName();
        $ret['groupUrl'] = $this->getGroupComponent()->getUrl();
        $ret['forum'] = $this->getForumComponent()->getName();
        $ret['forumUrl'] = $this->getForumComponent()->getUrl();
        return $ret;
    }

    public function mayModerate()
    {
        $authedUser = Zend_Registry::get('userModel')->getAuthedUser();
        if ($authedUser) {
            $t = new Vpc_Forum_ModeratorModel();
            $row = $t->fetchRow(array(
                'user_id = ?' => $authedUser->id,
                'group_id = ?' => $this->getCurrentPageKey()
            ));
            if ($row) return true;
        }
        return false;
    }

    protected function _getPagingComponent()
    {
        if (!isset($this->_paging)) {
            $classes = $this->_getSetting('childComponentClasses');
            $this->_paging = $this->createComponent($classes['paging'], 'paging');
            $select = $this->getTable()->getAdapter()->select();
            $select->from('vpc_forum_threads', array('count'=>'COUNT(*)'))
                ->where('component_id=?', $this->getDbId());
            $r = $select->query()->fetchAll();
            $this->_paging->setEntries($r[0]['count']);
        }
        return $this->_paging;
    }
    public function getChildComponents()
    {
        return array($this->_getPagingComponent());
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
