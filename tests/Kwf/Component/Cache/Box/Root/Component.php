<?php
class Kwf_Component_Cache_Box_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['generators']['child'] = array(
            'component' => 'Kwc_Basic_Empty_Component',
            'class' => 'Kwf_Component_Generator_Page_Static'
        );

        $ret['generators']['box'] = array(
            'component' => 'Kwf_Component_Cache_Box_Root_Box_Component',
            'class' => 'Kwf_Component_Generator_Box_Static',
            'inherit' => true
        );

        $ret['generators']['boxUnique'] = array(
            'component' => 'Kwf_Component_Cache_Box_Root_Box_Component',
            'class' => 'Kwf_Component_Generator_Box_Static',
            'inherit' => true,
            'unique' => true,
        );

        unset($ret['generators']['page']);
        return $ret;
    }
}
