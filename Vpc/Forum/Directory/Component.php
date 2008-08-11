<?php
class Vpc_Forum_Directory_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['groups'] = array(
            'class' => 'Vps_Component_Generator_Page_Table',
            'component' => 'Vpc_Forum_Group_Component'
        );
        $ret['generators']['users'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Forum_User_Directory_Component',
            'name' => 'users'
        );
        $ret['tablename'] = 'Vpc_Forum_Directory_Model';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $groups = $this->getData()->getChildComponents(array('generator' => 'groups'));
        $ret['groups'] = $this->_processGroups($groups);
        $ret['groupsTemplate'] = Vpc_Admin::getComponentFile(get_class($this), 'Groups', 'tpl');
        //$ret['searchUrl'] = $this->getPageFactory()->getChildPageById('search')->getUrl();
        return $ret;
    }
    
    private function _processGroups($groups, $parentId = null)
    {
        $ret = array();
        foreach ($groups as $group) {
            if ($group->row->parent_id == $parentId) {
                $lastThread = null;
                /*
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
                */
                $group->childGroups = $this->_processGroups($groups, $group->row->id);
                $ret[] = $group;
            }
        }
        return $ret;
    }
}
