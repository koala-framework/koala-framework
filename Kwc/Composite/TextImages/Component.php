<?php
class Kwc_Composite_TextImages_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName'     => 'TextImages',
            'componentIcon'     => new Kwf_Asset('textImages'),
            'ownModel'         => 'Kwc_Composite_TextImages_Model',
            'default'           => array(
                'image_position'    => 'left' // 'left', 'right', 'alternate'
            )
        ));
        $ret['assetsAdmin']['dep'][] = 'KwfTabPanel';
        $ret['generators']['child']['component']['text'] = 'Kwc_Basic_Text_Component';
        $ret['generators']['child']['component']['images'] = 'Kwc_List_Images_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['imagePosition'] = $this->_getRow()->image_position;
        return $return;
    }
}
