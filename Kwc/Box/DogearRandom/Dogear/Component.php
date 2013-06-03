<?php
class Kwc_Box_DogearRandom_Dogear_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Dogear');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';

        $ret['generators']['child']['component']['image'] = 'Kwc_Box_DogearRandom_Dogear_Image_Component';
        $ret['generators']['child']['component']['imageSmall'] = 'Kwc_Box_DogearRandom_Dogear_ImageSmall_Component';
        $ret['generators']['child']['component']['linkExtern'] = 'Kwc_Basic_LinkTag_Extern_Component';

        $ret['assets']['dep'][] = 'KwfOnReady';
        $ret['assets']['dep'][] = 'SwfObject';
        $ret['assets']['files'][] = 'kwf/Kwc/Box/DogearRandom/Dogear/Component.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $options = array();
        $options['colors'] = array(
            'color_small_1' => $this->_getRow()->color_small_1,
            'color_small_2' => $this->_getRow()->color_small_2,
            'color_big_1' => $this->_getRow()->color_big_1,
            'color_big_2' => $this->_getRow()->color_big_2
        );

        // images
        $options['urlSmall'] = $ret['imageSmall']->getComponent()->getImageUrl();
        $options['urlBig'] = $ret['image']->getComponent()->getImageUrl();
        // link
        $vars = $ret['linkExtern']->getComponent()->getTemplateVars();
        $options['linkUrl'] = $vars['data']->url;
        $options['linkOpen'] = $vars['data']->rel;

        $ret['options'] = $options;

        return $ret;
    }
}
