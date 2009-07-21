<?php
class Vps_View_Helper_IfHasNoContent extends Vps_View_Helper_IfHasContent
{
    protected $_tag = 'contentNo';
    public function ifHasNoContent(Vps_Component_Data $component = null)
    {
        return $this->ifHasContent($component);
    }
}
