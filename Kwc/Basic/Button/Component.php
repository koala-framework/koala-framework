<?php
class Kwc_Basic_Button_Component extends Kwc_Basic_Link_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['componentName'] = trlKwfStatic('Button');
        $ret['componentIcon'] = 'link_add';
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['styles'] = array(
            'default' => trlKwfStatic('Default'),
        );
        return $ret;
    }
    
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['style'] = $this->_getRow()->style;
        if (!$ret['style']) {
             $ret['style'] = key($this->_getSetting('styles'));
        }
        return $ret;
    }
}
