<?php
class Kwc_Composite_LinkImage_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = array_merge(parent::getSettings($param), array(
            'componentName'     => trlKwfStatic('Link Image'),
            'componentIcon'     => 'image'
        ));
        $ret['generators']['child']['component']['image'] = 'Kwc_Basic_Image_Component';
        $ret['generators']['child']['component']['link'] = 'Kwc_Basic_LinkTag_Component';
        return $ret;
    }
}
