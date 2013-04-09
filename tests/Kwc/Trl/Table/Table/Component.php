<?php
class Kwc_Trl_Table_Table_Component extends Kwc_Basic_Table_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['componentName'] = trlKwfStatic('Table');
        $ret['ownModel'] = 'Kwc_Trl_Table_Table_OwnModel';
        $ret['childModel'] = 'Kwc_Trl_Table_Table_MasterModel';

        return $ret;
    }
}
