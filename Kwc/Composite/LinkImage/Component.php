<?php
class Vpc_Composite_LinkImage_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName'     => trlVps('Link Image'),
            'componentIcon'     => new Vps_Asset('image')
        ));
        $ret['generators']['child']['component']['image'] = 'Vpc_Basic_Image_Component';
        $ret['generators']['child']['component']['link'] = 'Vpc_Basic_LinkTag_Component';
        return $ret;
    }
}
