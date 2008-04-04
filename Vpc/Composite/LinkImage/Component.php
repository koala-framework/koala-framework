<?php
class Vpc_Composite_LinkImage_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName'     => trlVps('Link Image'),
            'componentIcon'     => new Vps_Asset('image'),
            'tablename'         => 'Vpc_Composite_LinkImage_Model',
            'childComponentClasses' => array(
                'link'         => 'Vpc_Basic_LinkTag_Component',
                'image'        => 'Vpc_Composite_LinkImage_Image_Component',
            )
        ));
    }
}
