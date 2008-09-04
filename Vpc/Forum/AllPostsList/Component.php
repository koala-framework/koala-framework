<?php
class Vpc_Forum_AllPostsList_Component extends Vpc_Directories_List_Component
{
    protected function _getItemDirectory()
    {
        return 'Vpc_Forum_Posts_Directory_Component';
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
        
        $postGenerator->joinWithParentGenerator($ret, $threadGenerator, null, '-posts');
        $threadGenerator->joinWithParentGenerator($ret, $groupGenerator, $this->getData()->parent);
        return $ret;
    }
}
