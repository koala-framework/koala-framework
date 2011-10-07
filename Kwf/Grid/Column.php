<?php
class Vps_Grid_Column implements Vps_Collection_Item_Interface
{
    private $_properties;
    const ROLE_DISPLAY = 1;
    const ROLE_EXPORT = 2;
    const ROLE_PDF = 3;
    private $_data;

    //bitmask
    const SHOW_IN_GRID = 1;
    const SHOW_IN_PDF = 2;
    const SHOW_IN_XLS = 4;
    const SHOW_IN_CSV = 8;
    const SHOW_IN_ALL = 15;

    public function __construct($dataIndex = null, $header = null, $width = null)
    {
        if (!is_null($dataIndex)) $this->setDataIndex($dataIndex);
        if (!is_null($header)) $this->setHeader($header);
        if (!is_null($width)) $this->setWidth($width);
        $this->setShowIn(self::SHOW_IN_ALL);
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

    public function setEditor($ed)
    {
        if (is_string($ed)) {
            $ed = 'Vps_Form_Field_'.$ed;
            $ed = new $ed();
        }
        $ret = $this->setProperty('editor', $ed);
        if ($ed) {
            if (!$ed->getName()) $ed->setName($this->getDataIndex());
            $ed->setData($this->getData());
            $ed->trlStaticExecute();
        }
        return $ret;
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

    public function getMetaData($model, $tableInfo = null)
    {
        $ret = $this->_properties;

        foreach ($ret as $k=>$i) {
            if (is_object($i)) {
                unset($ret[$k]);
                if ($i instanceof Vps_Asset) {
                    $ret[$k] = $i->__toString();
                } else {
                    $ret[$k] = $i->getMetaData($model);
                }
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
            $ret['renderer'] = 'localizedDate';
        }

        if (isset($ret['showIn'])) unset($ret['showIn']);
        if (isset($ret['xlsOptions'])) unset($ret['xlsOptions']);
//todo:
//         if (isset($col['showDataIndex']) && $col['showDataIndex'] && !$this->_getColumnIndex($col['showDataIndex'])) {
//             $this->_columns[] = array('dataIndex' => $col['showDataIndex']);
//         }
        return $ret;
    }

    public function load($row, $role)
    {
        return $this->getData()->load($row);
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

    public function validate($row, $submitRow)
    {
        if ($this->getEditor()) {
            return $this->getEditor()->validate($row, $submitRow);
        } else {
            return array();
        }
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

    public function getData()
    {
        if (!isset($this->_data)) {
            $this->setData(new Vps_Data_Table());
        }
        return $this->_data;
    }

    public function setData(Vps_Data_Interface $data)
    {
        $this->_data = $data;
        $data->setFieldname($this->getDataIndex());
        if ($this->getEditor()) {
            $this->getEditor()->setData($data);
        }
        return $this;
    }
}
