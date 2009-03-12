<?php
/**
 * Dieser Generator sollte verwendet werden um die Errors_Component zu erstellen,
 * dadurch werden boxen die in dieser Komponente erstellt werden auch weitervererbt.
 */
class Vpc_Errors_Generator extends Vps_Component_Generator_Static
{
    protected function _formatConfig($parentData, $componentKey)
    {
        $data = parent::_formatConfig($parentData, $componentKey);
        $data['inherits'] = true;
        return $data;
    }
}
