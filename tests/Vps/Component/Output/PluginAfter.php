<?php
class Vps_Component_Output_PluginAfter extends Vps_Component_Output_Plugin
{
    public $type = self::EXECUTE_AFTER;
    
    public function processOutput($output)
    {
        // Da das Plugin nach dem Rendern ausgeführt wird, muss schon der
        // fertige Content hier reinkommen
        if ($output != 'root plugin(plugin(master2 child child2))') {
            return 'not ok from plugin';
        } else {
            return $output;
        }
    }
}
?>