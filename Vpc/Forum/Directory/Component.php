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
        $ret['tablename'] = 'Vpc_Forum_Directory_Model';
        $ret['componentName'] = trlVps('Forum');
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Forum/Directory/Panel.js';
        $ret['assetsAdmin']['dep'][] = 'VpsAutoTree';
        $ret['assetsAdmin']['dep'][] = 'VpsAutoAssignGrid';
        $ret['assets']['dep'][] = 'ExtCore';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['groups'] = $this->getGroups();
        $ret['groupsTemplate'] = Vpc_Admin::getComponentFile(get_class($this), 'Groups', 'tpl');
        //$ret['searchUrl'] = $this->getPageFactory()->getChildPageById('search')->getUrl();
        return $ret;
    }
    
    public function getGroups($parentId = null)
    {
        $ret = array();
        $groups = $this->getData()->getChildComponents(array('generator' => 'groups'));
        foreach ($groups as $group) {
            if ($group->row->parent_id == $parentId) {
                $group->lastPost = null;
                $group->lastUser = null;
                $group->countThreads = 0;
                $group->countPosts = 0;
                
                /* // ist wahrscheinlich zu langsam, wäre aber sauberer als Lösung unten
                $threads = $group->getChildComponents(array('generator' => 'detail'));
                $thread = array_shift($threads);
                if ($thread) {
                    $select = $thread->getGenerator('detail')->select($group, array('noDirectParent' => true));
                    $select->order('create_time DESC');
                    $posts = $thread->getChildComponents($select);
                    $group->lastPost = array_shift($posts);
                    $group->lastUser = $this->getData()->getChildComponent('_users')->getChildComponent('_' . $group->lastPost->row->user_id);
                    $group->countThreads = count($threads);
                    $group->countPosts = count($posts);
                }*/
                
                $root = Vps_Component_Data_Root::getInstance();
                $lastPostId = $group->row->getLastPostId();
                if ($lastPostId) {
                    $group->countPosts = $group->row->countPosts();
                    $group->countThreads = $group->row->countThreads();
                    $group->lastPost = $root->getComponentById($lastPostId);
                    if ($group->lastPost) {
                        $group->lastUser = $this->getData()->getChildComponent('_users')->getChildComponent('_' . $group->lastPost->row->user_id);
                    }
                }
                $group->childGroups = $this->getGroups($group->row->id);
                $ret[] = $group;
            }
        }
        return $ret;
    }
}
