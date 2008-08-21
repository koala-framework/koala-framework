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

        $ret['tablename'] = 'Vpc_Forum_Directory_Model';
        $ret['componentName'] = trlVps('Forum');
        $ret['placeholder']['searchButtonText'] = trlVps('go');
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
        $ret['search'] = $this->getData()->getChildComponent('_search');
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
                $threadGenerator = Vps_Component_Generator_Abstract
                    ::getInstance($group->componentClass, 'detail');
                $generators = Vpc_Abstract::getSetting($group->componentClass, 'generators');
                $postsGenerator = Vps_Component_Generator_Abstract
                    ::getInstance($generators['detail']['component'], 'detail');
                    
                // countThreads
                $select = $threadGenerator->select($group);
                $select->setIntegrityCheck(false);
                $select->reset(Zend_Db_Select::COLUMNS);
                $select->from(null, array('count' => "COUNT(*)"));
                $group->countThreads = $select->query()->fetchColumn(0);
                
                // countPosts
                $select = $postsGenerator->select(null);
                $select = $postsGenerator->joinWithParentGenerator($select, $threadGenerator, $group);
                $select->reset(Zend_Db_Select::COLUMNS);
                $select->from(null, array('count' => "COUNT(*)"));
                $group->countPosts = $select->query()->fetchColumn(0);

                // lastPost
                $select = $postsGenerator->select(null);
                $select = $postsGenerator->joinWithParentGenerator($select, $threadGenerator, $group);
                $select->order('vpc_posts.create_time DESC');
                $select->limit(1);
                $row = $select->query()->fetchAll();
                if ($row) {
                    $group->lastPost = Vps_Component_Data_Root::getInstance()
                        ->getComponentById($row[0]['component_id'] . '_' . $row[0]['id']);
                }
                
                // lastUser
                if ($group->lastPost) {
                    $group->lastUser = Vps_Component_Data_Root::getInstance()
                        ->getComponentByClass('Vpc_User_Directory_Component')
                        ->getChildComponent('_' . $group->lastPost->row->user_id);
                }
                
                $group->childGroups = $this->getGroups($group->row->id);
                
                $ret[] = $group;
            }
        }
        return $ret;
    }
}
