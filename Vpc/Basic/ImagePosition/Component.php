<?php
class Vpc_Basic_ImagePosition_Component extends Vpc_Abstract_Composite_Component 
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Image Positionable');
        $ret['componentIcon'] = new Vps_Asset('picture');
        $ret['generators']['child']['component'] = array(
            'image' => 'Vpc_Basic_Image_Component'
        );
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        return $ret;
    }
}
