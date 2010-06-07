<?php
class Vpc_Posts_Write_LastPosts_Component extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_Posts_Directory_View_Component';
        $ret['entryLimit'] = 5;
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent;
    }
    public function getSelect()
    {
        $select = parent::getSelect();
        $select->limit($this->_getSetting('entryLimit'));
        $select->order('id', 'DESC');
        return $select;
    }
}
