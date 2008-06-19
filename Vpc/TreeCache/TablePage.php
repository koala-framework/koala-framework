<?php
abstract class Vpc_TreeCache_TablePage extends Vpc_TreeCache_Table
{
    protected $_showInMenu = false;

    protected $_nameColumn= 'name';
    protected $_filenameColumn= 'filename';
    protected $_uniqueFilename = false;

    protected $_idSeparator = '_';
    protected $_pageDataClass = 'Vps_Component_Data_Page';
    
    protected function _formatConfig($parentData, $row)
    {
        $data = parent::_formatConfig($parentData, $row);
        $data['url'] = $row->{$this->_filenameColumn}; // TODO: uniqueFilename, hierarchische URL;
        $data['rel'] = ''; // TODO
        $data['name'] = $row->{$this->_nameColumn};
        $data['showInMenu'] = $this->_showInMenu;
        return $data;
    }
}
