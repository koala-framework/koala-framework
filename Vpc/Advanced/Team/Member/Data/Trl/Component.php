<?php
class Vpc_Advanced_Team_Member_Data_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponent)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['workingPosition'] = $this->_getRow()->working_position;
        $ret['vcard'] = $this->getData()->getChildComponent('_vcard');
        return $ret;
    }
}
