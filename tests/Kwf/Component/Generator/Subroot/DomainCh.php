<?php
class Kwf_Component_Generator_Subroot_DomainCh extends Kwf_Component_Generator_Subroot_Domain
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Kwf_Component_Generator_Subroot_CategoryCh';
        return $ret;
    }
}
?>