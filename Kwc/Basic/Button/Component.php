<?php
class Kwc_Basic_Button_Component extends Kwc_Basic_Link_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['componentName'] = trlKwfStatic('Button');
        $ret['componentIcon'] = new Kwf_Asset('link_add');
        $ret['styles'] = array(
            'default' => trlKwfStatic('Default'),
        );
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['style'] = $this->_getRow()->style;
        if (!$ret['style']) {
             $ret['style'] = key($this->_getSetting('styles'));
        }
        return $ret;
    }
}