<?php
class Kwc_Basic_Anchor_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentIcon'] = new Kwf_Asset('anchor');
        $ret['componentName'] = trlKwfStatic('Anchor');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars();
        $ret['anchor'] = $this->getRow()->anchor;
        return $ret;
    }
}
