<?php
class Vps_Component_Generator_PseudoPage_Table extends Vps_Component_Generator_Table
    implements Vps_Component_Generator_PseudoPage_Interface
{
    protected $_filenameColumn;
    protected $_uniqueFilename;
    protected $_nameColumn;

    protected function _init()
    {
        parent::_init();
        if (isset($this->_uniqueFilename)) {
            $this->_settings['uniqueFilename'] = $this->_uniqueFilename;
        }
        if (!isset($this->_settings['uniqueFilename'])) $this->_settings['uniqueFilename'] = false;

        if (isset($this->_filenameColumn)) {
            $this->_settings['filenameColumn'] = $this->_filenameColumn;
        }
        if (!isset($this->_settings['filenameColumn'])) $this->_settings['filenameColumn'] = false;

        if (isset($this->_nameColumn)) {
            $this->_settings['nameColumn'] = $this->_nameColumn;
        }
        if (!isset($this->_settings['nameColumn'])) $this->_settings['nameColumn'] = false;
    }

    protected function _formatConstraints($parentData, $constraints)
    {
        if (isset($constraints['filename'])) {
            $filename = $constraints['filename'];
            unset($constraints['filename']);
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
                $selectFields = $this->_getSelectFields();
                if (array_key_exists($this->_settings['filenameColumn'], $selectFields)) {
                    $select->where($selectFields[$this->_settings['filenameColumn']]. ' = ?', $constraints['filename']);
                } else {
                    $select->where($tableName.'.'.$this->_settings['filenameColumn'] . ' = ?', $constraints['filename']);
                }
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
        if ($this->_settings['nameColumn']) {
            $data['name'] = $row->{$this->_settings['nameColumn']};
        } else if (method_exists($row, '__toString')) {
            $data['name'] = $row->__toString();
        }

        if ($this->_settings['uniqueFilename']) {
            $data['filename'] = Vps_Filter::get($row->{$this->_settings['filenameColumn']}, 'Ascii');
        } else {
            $data['filename'] = $this->_getIdFromRow($row).'_';
            if ($this->_settings['filenameColumn']) {
                if (!isset($row->{$this->_settings['filenameColumn']})) {
                    throw new Vps_Exception("filenameColumn '".$this->_settings['filenameColumn']."' does not exist in row (Generator: ".get_class($this).")");
                }
                $data['filename'] .= Vps_Filter::get($row->{$this->_settings['filenameColumn']}, 'Ascii');
            } else if (isset($data['name'])) {
                $data['filename'] .= Vps_Filter::get($data['name'], 'Ascii');
            } else {
                throw new Vps_Exception("can't create filename for $this->_class");
            }
        }
        $data['rel'] = '';
        $data['isPseudoPage'] = true;
        return $data;
    }
}
