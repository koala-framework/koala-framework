<?p
class Vps_Auto_Grid_Column implements Vps_Collection_Item_Interfa

    private $_propertie
    const ROLE_DISPLAY = 
    const ROLE_EXPORT = 
    const ROLE_PDF = 
    private $_dat

    public function __construct($dataIndex = null, $header = null, $width = nul
   
        if ($dataIndex) $this->_properties['dataIndex'] = $dataInde
        if ($header) $this->_properties['header'] = $heade
        if ($width) $this->_properties['width'] = $widt
   

    public function __call($method, $argument
   
        if (substr($method, 0, 3) == 'set')
            if (!isset($arguments[0]))
                throw new Vps_Exception("Missing argument 1 (value)"
           
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4
            return $this->setProperty($name, $arguments[0]
        } else if (substr($method, 0, 3) == 'get')
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4
            return $this->getProperty($name
        } else
            throw new Vps_Exception("Invalid method called: '$method'"
       
   

    public function setProperty($name, $valu
   
        if ($name == 'editor' && is_string($value))
            $value = 'Vps_Auto_Field_'.$valu
            $value = new $value(
       
        if ($name == 'editor') 
            if (!$value->getName()) $value->setName($this->getDataIndex()
       
        $this->_properties[$name] = $valu
        return $thi
   

    public function getProperty($nam
   
        if (isset($this->_properties[$name]))
            return $this->_properties[$name
        } else
            return nul
       
   

    public function getMetaData($tableInfo = nul
   
        $ret = $this->_propertie

        foreach ($ret as $k=>$i)
            if (is_object($i))
                unset($ret[$k]
                $ret[$k] = $i->getMetaData(
           
       

        if (!isset($ret['type']))
            $ret['type'] = nul
       
        if ($tableIn
            && isset($tableInfo['metadata'][$this->getDataIndex()
            && strtolower($tableInfo['metadata'][$this->getDataIndex()]['DATA_TYPE']) == 'datetim
            && !$this->getDateFormat())
            $ret['dateFormat'] = 'Y-m-d H:i:s
       
        if ($ret['type'] == 'date' && !isset($ret['dateFormat']))
            $ret['dateFormat'] = 'Y-m-d
       
        if ($ret['type'] == 'date' && !isset($ret['renderer']))
            $ret['renderer'] = 'localizedDate
       
//tod
//         if (isset($col['showDataIndex']) && $col['showDataIndex'] && !$this->_getColumnIndex($col['showDataIndex']))
//             $this->_columns[] = array('dataIndex' => $col['showDataIndex']
//        
        return $re
   

    public function load($row, $rol
   
        return $this->getData()->load($row
   

    public function getName()
        return $this->getDataIndex(
   

    public function getByName($nam
   
        if ($this->getName() == $name)
            return $thi
        } else
            return nul
       
   

    public function hasChildren
   
        return fals
   

    public function getChildren
   
        return array(
   

    public function prepareSave($row, $submitRo
   
        if ($this->getEditor())
            $this->getEditor()->prepareSave($row, $submitRow
       
   

    public function save($row, $submitRo
   
        if ($this->getEditor())
            $this->getEditor()->save($row, $submitRow
       
   

    public function getData
   
        if (!isset($this->_data))
            $this->setData(new Vps_Auto_Data_Table()
       
        return $this->_dat
   

    public function setData(Vps_Auto_Data_Interface $dat
   
        $this->_data = $dat
        $data->setFieldname($this->getDataIndex()
        return $thi
   

