<?php
class Kwc_Abstract_Cards_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Abstract_Cards_Model';
        $ret['default']['component'] = 'none';
        $ret['generators']['child'] = array(
            'class' => 'Kwc_Abstract_Cards_Generator',
            'component' => array(),
        );
        $ret['assetsAdmin']['dep'][] = 'KwfFormCards';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/Cards/ComboBox.js';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        $ret['componentName'] = trlKwfStatic('Choose Child');
        $ret['apiContent'] = 'Kwc_Abstract_Cards_ApiContent';
        $ret['apiContentType'] = 'cards';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = array();
        $ret['child'] = $this->getData()->getChildComponent(array(
            'generator' => 'child'
        ));
        return $ret;
    }

    public function hasContent()
    {
        return $this->getData()->getChildComponent(array(
            'generator' => 'child'
        ))->hasContent();
    }
}
