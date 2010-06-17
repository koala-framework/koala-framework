<?php
class Vps_View_Helper_Partials
{
    public function partials($component, $partialClass = null, $params = array())
    {
        return Vps_Component_Output_Partials::getHelperOutput($component, $partialClass, $params);
    }
}
