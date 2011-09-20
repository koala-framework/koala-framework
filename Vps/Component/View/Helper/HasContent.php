<?php
class Vps_Component_View_Helper_HasContent extends Vps_Component_View_Helper_Abstract
{
    public function hasContent(Vps_Component_Data $target)
    {
        return $target->getComponent()->hasContent();
    }
}
