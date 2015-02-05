<?php
class Kwc_Basic_ImagePosition_Component extends Kwc_Abstract_Composite_Component 
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Image Positionable');
        $ret['componentIcon'] = 'picture';
        $ret['generators']['child']['component'] = array(
            'image' => 'Kwc_Basic_Image_Component'
        );
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }
}
