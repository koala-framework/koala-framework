<?php
class Kwf_Component_Cache_LinkTag_Intern_Root_TableItem_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Empty_Component'
        );
        return $ret;
    }
}
