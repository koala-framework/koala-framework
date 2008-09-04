<?php
class Vpc_Forum_Search_Component extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_Forum_Search_View_Component';
        $ret['order'] = array('field'=>'vpc_posts.create_time', 'dir'=>'DESC');
        return $ret;
    }
    protected function _getItemDirectory()
    {
        return 'Vpc_Forum_Group_Component';
    }
    public function getSelect()
    {
        $ret = parent::getSelect();

        $groupComponentClass = Vpc_Abstract::getChildComponentClass($this->getData()->parent->componentClass, 'groups');
        $threadComponentClass = Vpc_Abstract::getChildComponentClass($groupComponentClass, 'detail');
        $postsComponentClass = Vpc_Abstract::getChildComponentClass($threadComponentClass, 'child', 'posts');
        
        $groupGenerator = $this->getData()->parent->getGenerator('groups');
        $threadGenerator = Vps_Component_Generator_Abstract
                                ::getInstance($groupComponentClass, 'detail');
        $postGenerator = Vps_Component_Generator_Abstract
                                ::getInstance($postsComponentClass, 'detail');
                                                                
        $threadGenerator->joinWithParentGenerator($ret, $groupGenerator, $this->getData()->parent);
        $threadGenerator->joinWithChildGenerator($ret, $postGenerator, '-posts');
        $ret->group('vpc_forum_threads.id');
        
        return $ret;
    }
}
