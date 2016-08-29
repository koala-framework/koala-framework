<?php
class Kwc_Directories_AjaxViewTrl_Master_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['directory'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Directories_AjaxViewTrl_Directory_Component'
        );
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
