<?php
class Kwf_Component_Generator_Domain_DomainCh extends Kwf_Component_Generator_Domain_Domain
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Kwf_Component_Generator_Domain_CategoryCh';
        return $ret;
    }
}
?>