<?php
class Vps_Component_Generator_Page_Static extends Vps_Component_Generator_PseudoPage_Static
    implements Vps_Component_Generator_Page_Interface, Vps_Component_Generator_PseudoPage_Interface
{
    protected $_idSeparator = '_';
    protected $_inherits = true;

    protected function _formatConfig($parentData, $componentKey)
    {
        $data = parent::_formatConfig($parentData, $componentKey);
        $data['isPage'] = true;

        $c = $this->_settings;
        $data['name'] = isset($c['name']) ? $c['name'] : $componentKey;

        return $data;
    }
}
