<?php
class Vps_Component_Output_Plugin extends Vps_Component_Plugin_Abstract
    implements Vps_Component_Plugin_Interface_View
{
    public function processOutput($output)
    {
        return "plugin($output)";
    }
}
?>