<?php
class Kwc_Basic_BackgroundWindowWidth_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Background Window Width');
        $ret['componentCategory'] = 'layout';
        $ret['ownModel'] = 'Kwc_Basic_BackgroundWindowWidth_Model';
        $ret['generators']['child']['component']['image'] = 'Kwc_Basic_BackgroundWindowWidth_Image_Component';
        $ret['generators']['child']['component']['paragraphs'] = 'Kwc_Paragraphs_Component';
        $ret['editComponents'] = array('paragraphs');
        $ret['extConfig'] = 'Kwc_Basic_BackgroundWindowWidth_ExtConfig';

        $ret['backgroundColors'] = array(
            'none' => trlcKwfStatic('color', 'None'),
            'white' => trlKwfStatic('White'),
            'grey' => trlKwfStatic('Grey'),
            'black' => trlKwfStatic('Black')
        );

        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['backgroundColor'] = $this->_getBemClass('--' . $this->getRow()->background_color);
        $ret['marginBottom'] = $this->getRow()->margin_bottom;
        return $ret;
    }
}

