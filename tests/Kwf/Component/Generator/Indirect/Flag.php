<?php
class Kwf_Component_Generator_Indirect_Flag extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['foo'] = true;
        $ret['generators']['test'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_Indirect_Flag2'
        );
        return $ret;
    }
}
?>