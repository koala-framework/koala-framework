<?php
class Kwc_Basic_Anchor_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['flags']['hasAnchors'] = true;
        return $ret;
    }

    public function getAnchors()
    {
        return $this->getData()->chained->getComponent()->getAnchors();
    }
}
