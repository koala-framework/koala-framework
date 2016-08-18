<?php
class Kwc_List_Gallery_Trl_Component extends Kwc_Abstract_List_Trl_Component
{
    public static function getSettings($masterComponent = null)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['throwContentChangedOnOwnMasterModelUpdate'] = true;
        return $ret;
    }
}
