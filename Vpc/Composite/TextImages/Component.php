<?php
class Vpc_Composite_TextImages_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName'     => 'TextImages',
            'componentIcon'     => new Vps_Asset('textImages'),
            'ownModel'         => 'Vpc_Composite_TextImages_Model',
            'default'           => array(
                'image_position'    => 'left' // 'left', 'right', 'alternate'
            )
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsTabPanel';
        $ret['generators']['child']['component']['text'] = 'Vpc_Basic_Text_Component';
        $ret['generators']['child']['component']['images'] = 'Vpc_Composite_Images_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['imagePosition'] = $this->_getRow()->image_position;
        return $return;
    }
}
