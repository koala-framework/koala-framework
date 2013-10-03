<?php
class Kwc_Basic_Anchor_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentIcon'] = new Kwf_Asset('anchor');
        $ret['componentName'] = trlKwfStatic('Anchor');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['flags']['hasAnchors'] = true;
        return $ret;
    }

    public function getAnchors()
    {
        return array($this->getData()->componentId => $this->getRow()->anchor);
    }
}
