<?php
class Kwf_Component_Generator_PseudoPage_Table extends Kwf_Component_Generator_Table
{
    protected $_filenameColumn;
    protected $_uniqueFilename;
    protected $_nameColumn;
    protected $_maxFilenameLength;
    protected $_eventsClass = 'Kwf_Component_Generator_PseudoPage_Events_Table';

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

        if (isset($this->_maxFilenameLength)) {
            $this->_settings['maxFilenameLength'] = $this->_maxFilenameLength;
        }
        if (!isset($this->_settings['maxFilenameLength'])) $this->_settings['maxFilenameLength'] = 100;
    }

    protected function _formatSelectFilename(Kwf_Component_Select $select)
    {
        if ($select->hasPart(Kwf_Component_Select::WHERE_FILENAME)) {
            $filename = $select->getPart(Kwf_Component_Select::WHERE_FILENAME);
            if ($this->_settings['uniqueFilename']) {
                $select->whereEquals($this->_settings['filenameColumn'], $filename);
            } else {
                if ($this->_hasNumericIds) {
                    $pattern = '#^([0-9]+)[-_]#'; //_ for compatibility with older urls
                } else {
                    $pattern = '#^([^-_]+)[-_]#';
                }
                if (!preg_match($pattern, $filename, $m)) return null;
                $select->whereEquals($this->_idColumn, $m[1]);
            }
        }
        return $select;
    }

    protected function _getNameFromRow($row)
    {
        if ($this->_settings['nameColumn']) {
            return $row->{$this->_settings['nameColumn']};
        } else {
            return $row->__toString();
        }
    }

    protected function _getFilenameFromRow($row)
    {
        if ($this->_settings['filenameColumn']) {
            if (!isset($row->{$this->_settings['filenameColumn']})) {
                throw new Kwf_Exception("filenameColumn '".$this->_settings['filenameColumn']."' does not exist in row (Generator: ".get_class($this).")");
            }
            return $row->{$this->_settings['filenameColumn']};
        } else {
            return $this->_getNameFromRow($row);
        }
    }
    protected function _formatConfig($parentData, $row)
    {
        $data = parent::_formatConfig($parentData, $row);

        if (!$this->_settings['uniqueFilename']) {
            $data['filename'] = $this->_getIdFromRow($row).'-';
            $length = $this->_settings['maxFilenameLength'] - strlen($data['filename']);
            $data['filename'] .= Kwf_Filter::filterStatic($this->_getFilenameFromRow($row), 'Ascii', array($length));
        } else {
            //wenn uniqueFilename muss er exakt so belassen werden wie er ist
            //(weil danach ja die andere richtung gesucht wird)
            $data['filename'] = $this->_getFilenameFromRow($row);
        }
        $data['name'] = $this->_getNameFromRow($row);
        $data['rel'] = '';
        $data['isPseudoPage'] = true;
        return $data;
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['pseudoPage'] = true;
        return $ret;
    }

    public function getNameColumn()
    {
        return $this->_settings['nameColumn'];
    }

    public function getFilenameColumn()
    {
        return $this->_settings['filenameColumn'];
    }
}
