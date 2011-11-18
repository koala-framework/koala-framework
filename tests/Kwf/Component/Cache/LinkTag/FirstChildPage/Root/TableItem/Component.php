<?php
class Kwf_Component_Cache_LinkTag_FirstChildPage_Root_TableItem_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Empty_Component'
        );
        return $ret;
    }
}
