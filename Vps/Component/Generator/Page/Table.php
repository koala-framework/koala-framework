<?php
class Vps_Component_Generator_Page_Table extends Vps_Component_Generator_PseudoPage_Table
    implements Vps_Component_Generator_Page_Interface, Vps_Component_Generator_PseudoPage_Interface
{
    protected $_idSeparator = '_';

    protected function _formatConfig($parentData, $row)
    {
        $data = parent::_formatConfig($parentData, $row);
        $data['isPage'] = true;
        return $data;
    }
}
