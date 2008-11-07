<?php
class Vpc_Box_DogearRandom_Dogear_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Dogear');
        $ret['modelname'] = 'Vps_Component_FieldModel';

        $ret['generators']['child']['component']['image'] = 'Vpc_Box_DogearRandom_Dogear_Image_Component';
        $ret['generators']['child']['component']['imageSmall'] = 'Vpc_Box_DogearRandom_Dogear_ImageSmall_Component';
        $ret['generators']['child']['component']['linkExtern'] = 'Vpc_Basic_LinkTag_Extern_Component';

        $ret['assets']['dep'][] = 'ExtCore';
        $ret['assets']['dep'][] = 'SwfObject';
        $ret['assets']['files'][] = '/assets/vps/Vpc/Box/DogearRandom/Dogear/Component.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $ret['colorRow'] = $this->_getRow();

        // images
        $ret['urlSmall'] = $ret['imageSmall']->getComponent()->getImageUrl();
        $ret['urlBig'] = $ret['image']->getComponent()->getImageUrl();
        // link
        $vars = $ret['linkExtern']->getComponent()->getTemplateVars();
        $ret['linkUrl'] = $vars['data']->url;
        $ret['linkOpen'] = $vars['data']->rel;

        return $ret;
    }
}
