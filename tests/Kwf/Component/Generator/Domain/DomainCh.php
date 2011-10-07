<?php
class Vps_Component_Generator_Domain_DomainCh extends Vps_Component_Generator_Domain_Domain
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Vps_Component_Generator_Domain_CategoryCh';
        return $ret;
    }
}
?>