<?php
class Kwf_Component_Generator_Recursive_Static extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['static2'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_Recursive_Static2'
        );
        return $ret;
    }

}
