<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_Textfield_Component extends Kwc_Basic_Html_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlKwfStatic('Headline'),
            'ownModel' => 'Kwc_Basic_Textfield_Model'
        ));
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['content'] = str_replace('[-]', '&shy;', $ret['content']);
        return $ret;
    }
}
