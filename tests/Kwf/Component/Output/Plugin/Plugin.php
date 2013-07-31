<?php
class Kwf_Component_Output_Plugin_Plugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewBeforeChildRender
{
    public function processOutput($output, $renderer)
    {
        return "plugin($output)";
    }
}
?>
