<?php
class Kwf_Component_Output_Plugin_Plugin extends Kwf_Component_Plugin_View_Abstract
{
    public function processOutput($output)
    {
        return "plugin($output)";
    }
}
?>