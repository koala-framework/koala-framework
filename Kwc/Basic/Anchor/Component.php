<?php
class Kwc_Basic_Anchor_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentIcon'] = 'anchor';
        $ret['componentName'] = trlKwfStatic('Anchor');
        $ret['componentCategory'] = 'layout';
        $ret['componentPriority'] = 70;
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['flags']['hasAnchors'] = true;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['name'] = $this->getRow()->anchor ? $this->getRow()->anchor : null;
        $ret['anchorId'] = $this->getData()->componentId;
        return $ret;
    }

    public function getAnchors()
    {
        return array($this->getData()->componentId => $this->getRow()->anchor);
    }
}
