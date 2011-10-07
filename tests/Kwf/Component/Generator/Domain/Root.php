<?php
class Kwf_Component_Generator_Domain_Root extends Kwc_Root_DomainRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['domain']['component'] = array(
            'at' => 'Kwf_Component_Generator_Domain_Domain',
            'ch' => 'Kwf_Component_Generator_Domain_DomainCh'
        );
        $ret['generators']['domain']['model'] = 'Kwf_Component_Generator_Domain_DomainModel';
        unset($ret['generators']['box']);
        return $ret;
    }
}
?>