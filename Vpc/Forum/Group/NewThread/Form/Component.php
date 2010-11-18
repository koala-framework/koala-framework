<?php
class Vpc_Forum_Group_NewThread_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Vpc_Forum_Group_NewThread_Form_Success_Component';
        $ret['plugins'] = array('Vps_Component_Plugin_Login_Component');
        return $ret;
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        $row->component_id = $this->getData()->parent->parent->componentId;
    }
}
