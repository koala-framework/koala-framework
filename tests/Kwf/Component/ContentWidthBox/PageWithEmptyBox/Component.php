<?php
class Kwf_Component_ContentWidthBox_PageWithEmptyBox_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['testBox'] = array(
            'component' => 'Kwc_Basic_None_Component',
            'class' => 'Kwf_Component_Generator_Box_Static',
            'unique' => true,
            'inherit' => true
        );
        return $ret;
    }
}
