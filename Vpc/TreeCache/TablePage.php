<?php
abstract class Vpc_TreeCache_TablePage extends Vpc_TreeCache_Table
{
    protected $_showInMenu = false;

    protected $_nameColumn= 'name';
    protected $_filenameColumn= 'filename';
    protected $_uniqueFilename = false;

    protected $_idSeparator = '_';
    
    protected function _formatConfig($parentData, $row)
    {
        $data = parent::_formatConfig($parentData, $row);

         // TODO: uniqueFilename, hierarchische URL;
        $data['filename'] = $row->{$this->_filenameColumn};

        $data['rel'] = '';
        $data['name'] = $row->{$this->_nameColumn};
        $data['isPage'] = true;
        return $data;
    }
    protected function _formatConstraints($parentData, $constraints)
    {
        if (isset($constraints['page'])) {
            if (!$constraints['page']) return null;
            unset($constraints['page']);
        }
        if (isset($constraints['showInMenu'])) {
            if ($constraints['showInMenu'] && !$this->_showInMenu) return null;
            if (!$constraints['showInMenu'] && $this->_showInMenu) return null;
            unset($constraints['showInMenu']);
        }
        return parent::_formatConstraints($parentData, $constraints);
    }
}
