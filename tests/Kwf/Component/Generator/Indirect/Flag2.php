<?php
class Kwf_Component_Generator_Indirect_Flag2 extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['foo'] = true;
        $ret['flags']['bar'] = true;
        $ret['generators']['test'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_Indirect_Flag3'
        );
        return $ret;
    }
}
?>