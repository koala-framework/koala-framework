<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_Textfield_Component extends Kwc_Basic_Html_Component
{
    public static function getSettings($param = null)
    {
        return array_merge(parent::getSettings($param), array(
            'componentName' => trlKwfStatic('Headline'),
            'ownModel' => 'Kwc_Basic_Textfield_Model',
            'apiContent' => 'Kwc_Basic_Textfield_ApiContent',
            'apiContentType' => 'text'
        ));
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['content'] = str_replace('[-]', '&shy;', $ret['content']);
        return $ret;
    }

    public function getFulltextContent()
    {
        $ret = parent::getFulltextContent();
        $ret['content'] = str_replace('[-]', '&shy;', $ret['content']);
        $ret['normalContent'] = str_replace('[-]', '&shy;', $ret['normalContent']);
        return $ret;
    }
}
