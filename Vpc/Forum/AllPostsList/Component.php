<?php
class Vpc_Forum_AllPostsList_Component extends Vpc_Directories_List_Component
{
    protected function _getItemDirectory()
    {
        return 'Vpc_Forum_Thread_Directory_Component';
    }
    public function getSelect()
    {
        $ret = parent::getSelect();
        $groupGenerator = $this->getData()->parent->getGenerator('groups');
        $groupComponentClass = Vpc_Abstract::getChildComponentClass($this->getData()->parent->componentClass, 'groups');
        $threadGenerator = Vps_Component_Generator_Abstract
                                ::getInstance($groupComponentClass, 'detail');
        $threadComponentClass = Vpc_Abstract::getChildComponentClass($groupComponentClass, 'detail');
        $postGenerator = Vps_Component_Generator_Abstract
                                ::getInstance($threadComponentClass, 'detail');
        $threadGenerator->joinWithChildGenerator($ret, $postGenerator);
        $groupGenerator->joinWithChildGenerator($ret, $threadGenerator, $this->getData()->parent);
        return $ret;
    }
}
