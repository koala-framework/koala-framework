<?php
class Vps_Component_Generator_Page_Table extends Vps_Component_Generator_Table implements Vps_Component_Generator_Page_Interface
{
    protected $_idSeparator = '_';
    protected $_nameColumn;
    protected $_filenameColumn;
    protected $_uniqueFilename;
    
    protected function _init()
    {
        parent::_init();
        $this->_settings['uniqueFilename'] = $this->_uniqueFilename;
        $this->_settings['nameColumn'] = $this->_nameColumn;
        $this->_settings['filenameColumn'] = $this->_filenameColumn;
    }
    
    protected function _formatConstraints($parentData, $constraints)
    {
        if (isset($constraints['filename'])) {
            $filename = $constraints['filename'];
            unset($constraints['filename']);
        }
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
        $constraints = parent::_formatConstraints($parentData, $constraints);
        if (isset($filename)) { $constraints['filename'] = $filename; }

        return $constraints;
    }

    protected function _getSelect($parentData, $constraints)
    {
        $select = parent::_getSelect($parentData, $constraints);
        $tableName = $this->_table->info('name');
        if (!$select) return null;
        if (isset($constraints['filename'])) {
            if ($this->_settings['uniqueFilename']) {
                $select->where($tableName.'.'.$this->_settings['filenameColumn'] . ' = ?', $constraints['filename']);
            } else {
                if (!preg_match('#^([0-9]+)_#', $constraints['filename'], $m)) return null;
                $select->where($tableName.'.'.$this->_idColumn . ' = ?', $m[1]);
            }
        }
        return $select;
    }

    protected function _formatConfig($parentData, $row)
    {
        $data = parent::_formatConfig($parentData, $row);
        $data['row'] = $row;
        
        if ($this->_settings['nameColumn']) {
            $data['name'] = $row->{$this->_settings['nameColumn']};
        } else {
            $data['name'] = $row->__toString();
        }
        if ($this->_settings['uniqueFilename']) {
            $data['filename'] = $row->{$this->_settings['filenameColumn']};
        } else {
            $data['filename'] = $this->_getIdFromRow($row).'_';
            if (isset($this->_settings['filenameColumn'])) {
                if (!isset($row->{$this->_settings['filenameColumn']})) {
                    throw new Vps_Exception("filenameColumn '".$this->_settings['filenameColumn']."' does not exist in row (Generator: ".get_class($this).")");
                }
                $data['filename'] .= $row->{$this->_settings['filenameColumn']};
            } else {
                $data['filename'] .= Vps_Filter::get($data['name'], 'Ascii');
            }
        }
        $data['rel'] = '';
        $data['isPage'] = true;
        return $data;
    }
}
