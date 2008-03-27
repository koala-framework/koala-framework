<?php
class Vpc_Forum_Component extends Vpc_Abstract
{
    private $_groups;
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName'         => 'Forum',
            'tablename'             => 'Vpc_Forum_Group_Model',
            'childComponentClasses' => array(
                'group' => 'Vpc_Forum_Group_Component',
                'user'  => 'Vpc_Forum_User_Component'
            ),
            'loginDecorator' => 'Vpc_Decorator_CheckLogin_Component'
        ));
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Forum/Panel.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        if (!(Zend_Registry::get('userModel')->getAllCache())) {
            Zend_Registry::get('userModel')->createAllCache();
        }

        $ret = parent::getTemplateVars();
        $where = array();
        $where['component_id = ?'] = $this->getDbId();
        if (!$this->_showInvisible()) {
            $where[] = 'visible = 1';
        }
        $this->_groups = $this->getTable()->fetchAll($where, 'pos');
        $ret['groups'] = $this->_processGroups(null);
        $ret['groupsTemplate'] = Vpc_Admin::getComponentFile(get_class($this), 'Groups', 'tpl');
        return $ret;
    }

    private function _processGroups($parentId)
    {
        $threadT = new Vpc_Forum_Thread_Model();
        $postsT = new Vpc_Posts_Model();
        $groups = array();
        foreach ($this->_groups->toArray() as $row) {
            //toArray weil sonst funktioniert rekursiver aufruf nicht
            $row = (object)$row;
            if ($row->parent_id == $parentId) {
                $page = $this->getPageFactory()->getChildPageByRow($row);
                $lastThread = null;
                $g = array(
                    'name' => $row->name,
                    'description' => $row->description,
                    'post' => $row->post,
                    'url' => $page->getUrl()
                );
                if ($row->post) {
                    $where = array();
                    $where['component_id = ?'] = $page->getDbId();
                    $lastThread = $threadT->fetchAll($where, null, 1)->current();
                    $g['numPosts'] = $threadT->getNumPosts($page->getDbId());
                    $g['numThreads'] = $threadT->getNumThreads($page->getDbId());
                }
                if ($lastThread) {
                    $post = $postsT->getLastPost($lastThread->component_id.'_'.$lastThread->id.'-posts');
                    $g['lastPostTime'] = $post->create_time;
                    $forumUserTable = new Vpc_Forum_User_Model();
                    $forumUser = $forumUserTable->fetchRow(array('id = ?' => $post->user_id));
                    $user = Zend_Registry::get('userModel')->find($post->user_id)->current();
                    if ($user) {
                        if ($forumUser->nickname) {
                            $g['lastPostUser'] = $forumUser->nickname;
                        } else {
                            $g['lastPostUser'] = $user->firstname;
                        }
                        $g['lastPostUserUrl'] = $this->getUserViewComponent($forumUser)->getUrl();
                    } else {
                        $g['lastPostUser'] = 'Anonym';
                        $g['lastPostUserUrl'] = null;
                    }
                    $page = $page->getPageFactory()->getChildPageByRow($lastThread);
                    $g['lastPostUrl'] = $page->getUrl();
                    $g['lastPostSubject'] = $lastThread->subject;
                } else {
                    $g['lastPostUrl'] = null;
                    $g['lastPostSubject'] = null;
                    $g['lastPostTime'] = null;
                    $g['lastPostUser'] = null;
                    $g['lastPostUserUrl'] = null;
                }
                $g['childGroups'] = $this->_processGroups($row->id);
                $groups[] = $g;
            }
        }
        return $groups;
    }

    public function getUserViewComponent($user)
    {
        return $this->getPageFactory()->getChildPageById('users')
                    ->getPageFactory()->getChildPageByRow($user);
    }
}
