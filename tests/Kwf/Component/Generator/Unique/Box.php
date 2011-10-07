<?php
class Kwf_Component_Generator_Unique_Box extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_Unique_Page',
            'name' => 'page'
        );
        return $ret;
    }

}
