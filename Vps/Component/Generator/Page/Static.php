<?php
class Vps_Component_Generator_Page_Static extends Vps_Component_Generator_PseudoPage_Static
    implements Vps_Component_Generator_Page_Interface, Vps_Component_Generator_PseudoPage_Interface
{
    protected $_idSeparator = '_';

    protected function _formatConfig($parentData, $componentKey)
    {
        $data = parent::_formatConfig($parentData, $componentKey);
        $data['isPage'] = true;
        $data['inherits'] = true;

        $data['name'] = isset($c['name']) ? $c['name'] : $componentKey;

        return $data;
    }
}
