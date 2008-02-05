<?php
class Vpc_Composite_TextImages_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName'     => 'TextImages',
            'tablename'         => 'Vpc_Composite_TextImages_Model',
            'childComponentClasses' => array(
                'text'         => 'Vpc_Basic_Text_Component',
                'images'       => 'Vpc_Composite_Images_Component',
            ),
            'default'           => array(
                'image_position'    => 'left' // 'left', 'right', 'alternate'
            )
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsTabPanel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['imagePosition'] = $this->_getRow()->image_position;
        return $return;
    }
}
