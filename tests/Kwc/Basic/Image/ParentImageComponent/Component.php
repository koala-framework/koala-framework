<?php
class Kwc_Basic_Image_ParentImageComponent_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Basic_Image_TestModel';
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Basic_Image_ParentImageComponent_Child_Component'
        );
        $ret['dimensions'] = array(array('width'=>100, 'height'=>100, 'cover' => true));
        return $ret;
    }
}
