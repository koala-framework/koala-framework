<?php
class Kwf_Component_Output_Plugin_PluginAfter extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewAfterChildRender
{
    public function processOutput($output, $renderer)
    {
        // Da das Plugin nach dem Rendern ausgeführt wird, muss schon der
        // fertige Content hier reinkommen
        if ($output != 'root plugin(plugin(child child2))') {
            return "not ok from plugin";
        } else {
            return "afterPlugin($output)";
        }
    }
}
