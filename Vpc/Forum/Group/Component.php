<?php
class Vpc_Forum_Group_Component extends Vpc_Abstract_Composite_Component
    implements Vpc_Paging_ParentInterface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['thread'] = array(
            'class' => 'Vps_Component_Generator_Page_Table',
            'component' => 'Vpc_Forum_Thread_Component',
            'table' => 'Vpc_Forum_Group_Model'
        );
        $ret['generators']['newThread'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Forum_Group_NewThread_Component',
            'name' => trlVps('new thread')
        );
        $ret['generators']['child']['component']['paging'] = 'Vpc_Paging_Component';
        return $ret;
    }
    public function getPagingCount()
    {
        return $this->_getSelect();
    }
    private function _getSelect()
    {
        return $this->getData()->getGenerator('thread')->select($this->getData());
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $select = $this->_getSelect();
        $this->getData()->getChildComponent('-paging')->getComponent()
                                            ->limitSelect($select);
        $ret['threads'] = $this->getData()->getChildComponents($select);
        $ret['newThread'] = $this->getData()->getChildComponent('_newThread');
/*
        $ret['newThreadUrl'] = $this->getPageFactory()
                                    ->getChildPageById('newthread')->getUrl();

        $ret['group'] = $this->getGroupComponent()->getName();
        $ret['groupUrl'] = $this->getGroupComponent()->getUrl();
        $ret['forum'] = $this->getForumComponent()->getName();
        $ret['forumUrl'] = $this->getForumComponent()->getUrl();
*/
        return $ret;
    }
}
