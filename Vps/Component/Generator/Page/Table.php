<?php
class Vps_Component_Generator_Page_Table extends Vps_Component_Generator_PseudoPage_Table
    implements Vps_Component_Generator_Page_Interface
{
    protected $_idSeparator = '_';
    
    protected function _init()
    {
        parent::_init();
    }
    
    protected function _formatConstraints($parentData, $constraints)
    {
        if (isset($constraints['showInMenu'])) {
            if ($constraints['showInMenu'] &&
                (!isset($this->_settings['showInMenu']) || !$this->_settings['showInMenu']))
            {
                return null;
            }
            if (!$constraints['showInMenu'] && 
                isset($this->_settings['showInMenu']) && $this->_settings['showInMenu'])
            {
                return null;
            }
            unset($constraints['showInMenu']);
        }
        return parent::_formatConstraints($parentData, $constraints);
    }

    protected function _formatConfig($parentData, $row)
    {
        $data = parent::_formatConfig($parentData, $row);
        $data['isPage'] = true;
        return $data;
    }
}
