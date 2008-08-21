<?php
class Vpc_Forum_Search_Component extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_Forum_Search_View_Component';
        $ret['order'] = 'vpc_posts.create_time DESC';
        return $ret;
    }
    protected function _getItemDirectory()
    {
        return 'Vpc_Forum_Group_Component';
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
        $postGenerator->joinWithParentGenerator($ret, $threadGenerator);
        $groupGenerator->joinWithChildGenerator($ret, $threadGenerator, $this->getData()->parent);
        return $ret;
    }
}
