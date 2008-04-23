<?php
class Vpc_Composite_TextImage_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName'     => trlVps('TextImage'),
            'componentIcon'     => new Vps_Asset('textImage'),
            'tablename'         => 'Vpc_Composite_TextImage_Model',
            'childComponentClasses' => array(
                'text'         => 'Vpc_Basic_Text_Component',
                'image'        => 'Vpc_Basic_Image_Component',
            ),
            'default'           => array(
                'image_position'    => 'left' // 'left', 'right', 'alternate'
            )
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['image_position'] = $this->_getRow()->image_position;
        return $return;
    }

    public function getTextImageRow()
    {
        return $this->_getRow();
    }
}
