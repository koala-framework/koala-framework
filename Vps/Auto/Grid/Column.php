<?php
class Vps_Auto_Grid_Column implements Vps_Collection_Item_Interface
{
    private $_properties;
    const ROLE_DISPLAY = 1;
    const ROLE_EXPORT = 2;
    const ROLE_PDF = 3;

    public function __construct($dataIndex = null, $header = null, $width = null)
    {
        if ($dataIndex) $this->_properties['dataIndex'] = $dataIndex;
        if ($header) $this->_properties['header'] = $header;
        if ($width) $this->_properties['width'] = $width;
    }

    public function __call($method, $arguments)
    {
        if (substr($method, 0, 3) == 'set') {
            if (!isset($arguments[0])) {
                throw new Vps_Exception("Missing argument 1 (value)");
            }
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            return $this->setProperty($name, $arguments[0]);
        } else if (substr($method, 0, 3) == 'get') {
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            return $this->getProperty($name);
        } else {
            throw new Vps_Exception("Invalid method called: '$method'");
        }
    }

    public function setProperty($name, $value)
    {
        $this->_properties[$name] = $value;
        return $this;
    }

    public function getProperty($name)
    {
        if (isset($this->_properties[$name])) {
            return $this->_properties[$name];
        } else {
            return null;
        }
    }

    public function getMetaData($tableInfo = null)
    {
        $ret = $this->_properties;

        foreach ($ret as $k=>$i) {
            if (is_object($i)) {
                unset($ret[$k]);
                $ret[$k] = $i->getMetaData();
            }
        }

        if (!isset($ret['type'])) {
            $ret['type'] = null;
        }
        if ($tableInfo
            && isset($tableInfo['metadata'][$this->getDataIndex()])
            && strtolower($tableInfo['metadata'][$this->getDataIndex()]['DATA_TYPE']) == 'datetime'
            && !$this->getDateFormat()) {
            $ret['dateFormat'] = 'Y-m-d H:i:s';
        }
        if ($ret['type'] == 'date' && !isset($ret['dateFormat'])) {
            $ret['dateFormat'] = 'Y-m-d';
        }
        if ($ret['type'] == 'date' && !isset($ret['renderer'])) {
            $ret['renderer'] = 'Date';
        }
//todo:
//         if (isset($col['showDataIndex']) && $col['showDataIndex'] && !$this->_getColumnIndex($col['showDataIndex'])) {
//             $this->_columns[] = array('dataIndex' => $col['showDataIndex']);
//         }
        return $ret;
    }

    public function getData($row, $role)
    {
        $dataIndex = $this->getDataIndex();
        if (!is_null($row->$dataIndex) && !isset($row->$dataIndex)) {
            throw new Vps_Exception("Index '$dataIndex' not found in row");
        }
        return $row->$dataIndex;
    }

    public function setEditor($editor)
    {
        if (is_string($editor)) {
            $editor = 'Vps_Auto_Field_'.$editor;
            $editor = new $editor();
        }
        if (!$editor->getName()) $editor->setName($this->getDataIndex());
        return $this->setProperty('editor', $editor);
    }

    public function getName() {
        return $this->getDataIndex();
    }

    public function getByName($name)
    {
        if ($this->getName() == $name) {
            return $this;
        } else {
            return null;
        }
    }

    public function hasChildren()
    {
        return false;
    }

    public function getChildren()
    {
        return array();
    }

    public function prepareSave($row, $submitRow)
    {
        if ($this->getEditor()) {
            $this->getEditor()->prepareSave($row, $submitRow);
        }
    }

    public function save($row, $submitRow)
    {
        if ($this->getEditor()) {
            $this->getEditor()->save($row, $submitRow);
        }
    }
}
