<?php
class Vps_Component_Output_Plugin_Plugin extends Vps_Component_Plugin_View_Abstract
{
    public function processOutput($output)
    {
        return "plugin($output)";
    }
}
?>