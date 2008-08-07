<?php
class Vpc_Forum_Group_Component extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'Vpc_Forum_Thread_Component';
        $ret['generators']['newThread'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Forum_Group_NewThread_Component',
            'name' => trlVps('new thread')
        );
        $ret['tablename'] = 'Vpc_Forum_Group_Model';
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
        $ret['newThread'] = $this->getData()->getChildComponent('_newThread');
        return $ret;
    }
}
