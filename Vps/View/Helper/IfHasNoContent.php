<?php
class Vps_View_Helper_IfHasNoContent
{
    public function ifHasNoContent(Vps_Component_Data $component = null)
    {
        // nicht über Vererbung, da sonst bei IfHasContent das Static nicht richtig gezählt wird
        $content = new Vps_View_Helper_IfHasContent();
        return $content->ifHasContent($component, 'contentNo');
    }
}
