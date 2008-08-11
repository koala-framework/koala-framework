<?php
class Vpc_Posts_Write_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Vpc_Posts_Write_Form_Success_Component';
        $ret['tablename'] = 'Vpc_Posts_Directory_Model';
        return $ret;
    }

    protected function _getPostsComponent()
    {
        return $this->getData()->parent->parent;
    }
    
    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        $row->component_id = $this->_getPostsComponent()->componentId;
    }
}
