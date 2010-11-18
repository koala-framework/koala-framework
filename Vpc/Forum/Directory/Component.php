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

        $ret['generators']['search'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Forum_Search_Component',
            'name' => trlVps('Search'),
            'showInMenu' => true
        );

        $ret['generators']['feedList'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Forum_FeedList_Component'
        );

        $ret['childModel'] = 'Vpc_Forum_Directory_Model';
        $ret['componentName'] = trlVps('Forum.Forum');
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Forum/Directory/Panel.js';
        $ret['assetsAdmin']['dep'][] = 'VpsAutoTree';
        $ret['assetsAdmin']['dep'][] = 'VpsAutoAssignGrid';
        $ret['assets']['dep'][] = 'ExtCore';
        $ret['flags']['processInput'] = true;

        return $ret;
    }

    public function processInput($postData)
    {
        $this->getData()->getChildComponent('_search')
                ->getChildComponent('-view')
                ->getChildComponent('-searchForm')
                ->getComponent()->processInput($postData);
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['groups'] = $this->getGroups();
        $ret['groupsTemplate'] = Vpc_Admin::getComponentFile(get_class($this), 'Groups', 'tpl');
        $ret['searchForm'] = $this->getData()->getChildComponent('_search')
                ->getChildComponent('-view')->getChildComponent('-searchForm');
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

                // Generators holen
                $groupComponentClass = $group->componentClass;
                $threadComponentClass = Vpc_Abstract::getChildComponentClass($groupComponentClass, 'detail');
                $postsComponentClass = Vpc_Abstract::getChildComponentClass($threadComponentClass, 'child', 'posts');
                $threadGenerator = Vps_Component_Generator_Abstract
                    ::getInstance($groupComponentClass, 'detail');
                $postsGenerator = Vps_Component_Generator_Abstract
                    ::getInstance($postsComponentClass, 'detail');

                // countThreads
                $select = $threadGenerator->select($group);
                $group->countThreads = $threadGenerator->countChildData($group, $select);

                // countPosts
                $select = $postsGenerator->select(null);
                $select = $postsGenerator->joinWithParentGenerator($select, $threadGenerator, $group);
                $group->countPosts = $postsGenerator->countChildData(null, $select);

                // lastPost
                $select = $postsGenerator->select(null);
                $select = $postsGenerator->joinWithParentGenerator($select, $threadGenerator, $group);
                $select->order('create_time', 'DESC');
                $select->limit(1);
                $group->lastPost = current($postsGenerator->getChildData(null, $select));

                // lastUser
                if ($group->lastPost) {
                    $group->lastUser = Vps_Component_Data_Root::getInstance()
                        ->getComponentByClass(
                            'Vpc_User_Directory_Component',
                            array('subroot' => $this->getData())
                        )
                        ->getChildComponent('_' . $group->lastPost->row->user_id);
                }

                $group->childGroups = $this->getGroups($group->row->id);

                $ret[] = $group;
            }
        }
        return $ret;
    }

    public static function getStaticCacheVars($calledClass)
    {
        $ret = array();
        $class = Vpc_Abstract::getChildComponentClass($calledClass, 'groups');
        $class = Vpc_Abstract::getChildComponentClass($class, 'detail');
        $class = Vpc_Abstract::getChildComponentClass($class, 'child', 'posts');
        $postsModel = Vpc_Abstract::createModel($class);
        $ret[] = array(
            'model' => $postsModel
        );
        $ret[] = array(
            'model' => Vps_Registry::get('config')->user->model
        );
        return $ret;
    }
}
