<?php
class Vps_Model_FnFFile extends Vps_Model_FnF
{
    protected $_fileName;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        if (isset($config['fileName'])) {
            $this->_fileName = 'application/temp/fnf-file-'.$config['fileName'];
        }
        if (!$this->_fileName && $this->_uniqueIdentifier) {
            $this->_fileName = 'application/temp/fnf-file-'.$this->_uniqueIdentifier;
        }
        if (!$this->_fileName) {
            if (get_class($this) == 'Vps_Model_FnFFile') {
                throw new Vps_Exception("Inhert from Vps_Model_FnFFile or set an filename/uniqueIdentifier");
            }
            $this->_fileName = 'application/temp/fnf-file-'.get_class($this);
        }
    }

    protected function _dataModified()
    {
        file_put_contents($this->_fileName, serialize($this->_data));
    }

    public function getData()
    {
        clearstatcache();
        if (file_exists($this->_fileName)) {
            $this->_data = unserialize(file_get_contents($this->_fileName));
        }
        if (!$this->_data) $this->_data = array();
        foreach ($this->_rows as $key=>$row) {
            if (!isset($this->_data[$key])) {
                unset($this->_rows[$key]);
            } else {
                $row->setData($this->_data[$key]);
            }
        }
        return $this->_data;
    }
}
