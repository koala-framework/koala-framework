<?php
class Kwf_Component_View_Helper_HasContent extends Kwf_Component_View_Helper_Abstract
{
    public function hasContent(Kwf_Component_Data $target)
    {
        return $target->hasContent();
    }
}
