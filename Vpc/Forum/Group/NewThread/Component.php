<?php
class Vpc_Forum_Group_NewThread_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Vpc_Forum_Group_NewThread_Success_Component';
        $ret['tablename'] = 'Vpc_Forum_Group_Model';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        $row->component_id = $this->getData()->parent->componentId;
    }    
}