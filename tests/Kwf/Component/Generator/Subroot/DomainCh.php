<?php
class Vps_Component_Generator_Subroot_DomainCh extends Vps_Component_Generator_Subroot_Domain
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Vps_Component_Generator_Subroot_CategoryCh';
        return $ret;
    }
}
?>