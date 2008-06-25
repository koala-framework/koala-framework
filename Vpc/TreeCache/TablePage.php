<?php
abstract class Vpc_TreeCache_TablePage extends Vpc_TreeCache_Table
{
    protected $_showInMenu = false;

    protected $_nameColumn= 'name';
    protected $_filenameColumn= 'filename';
    protected $_uniqueFilename = false;

    protected $_idSeparator = '_';
    
    protected function _formatConstraints($parentData, $constraints)
    {
        if (isset($constraints['page']) && !$constraints['page']) {
            return null;
        } else {
            unset($constraints['page']);
        }
        if (isset($constraints['filename'])) {
            $filename = $constraints['filename'];
            unset($constraints['filename']);
        }
        if (isset($constraints['showInMenu'])) {
            if ($constraints['showInMenu'] && !$this->_showInMenu) return null;
            if (!$constraints['showInMenu'] && $this->_showInMenu) return null;
            unset($constraints['showInMenu']);
        }
        $constraints = parent::_formatConstraints($parentData, $constraints);
        if (isset($filename)) { $constraints['filename'] = $filename; }

        return $constraints;
    }

    protected function _getSelect($parentData, $constraints)
    {
        $select = parent::_getSelect($parentData, $constraints);
        if (!$select) return null;
        if (isset($constraints['filename'])) {
            $select->where($this->_filenameColumn . ' = ?', $constraints['filename']);
        }
        return $select;
    }

    protected function _formatConfig($parentData, $row)
    {
        $data = parent::_formatConfig($parentData, $row);

         // TODO: uniqueFilename
        $data['filename'] = $row->{$this->_filenameColumn};

        $data['rel'] = '';
        $data['name'] = $row->{$this->_nameColumn};
        $data['isPage'] = true;
        return $data;
    }
    public function createsPages()
    {
        return true;
    }
}
