<?php
class Kwf_Component_Cache_CacheDisabled_Root extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Cache_CacheDisabled_Test_Component',
        );
        return $ret;
    }
}
