<?p
abstract class Vps_Auto_Field_Abstract implements Vps_Collection_Item_Interfa

    private $_propertie
    protected $_validators = array(
    private $_dat

    public function __construct($field_name = null, $field_label = nul
   
        if ($field_name) $this->setProperty('name', $field_name
        if ($field_label) $this->setProperty('fieldLabel', $field_label
   

    public function __call($method, $argument
   
        if (substr($method, 0, 3) == 'set')
            if (!isset($arguments[0]) && !is_null($arguments[0]))
                throw new Vps_Exception("Missing argument 1 (value)"
           
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4
            return $this->setProperty($name, $arguments[0]
        } else if (substr($method, 0, 3) == 'get')
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4
            return $this->getProperty($name
        } else
            throw new Vps_Exception("Invalid method called: '$method'"
       
   

    public function setProperty($name, $valu
   
        $this->_properties[$name] = $valu
        return $thi
   

    public function getProperty($nam
   
        if (isset($this->_properties[$name]))
            return $this->_properties[$name
        } else
            return nul
       
   

    public function getMetaData
   
        $ret = $this->_propertie
        if (isset($ret['name']))
            $ret['name'] = $this->getFieldName(
       
        if (isset($ret['hiddenName']) && $this->getNamePrefix())
            $ret['hiddenName'] = $this->getNamePrefix() . '_' . $ret['hiddenName'
       
        if (isset($ret['namePrefix'])) unset($ret['namePrefix']
        return $re
   

    public function load($ro
   
        $ret = array(
        if ($this->hasChildren())
            foreach ($this->getChildren() as $field)
                $ret = array_merge($ret, $field->load($row)
           
       
        return $re
   

    public function prepareSave($row, $postDat
   
        $this->_addValidators(

        if ($this->hasChildren())
            foreach ($this->getChildren() as $field)
                $field->prepareSave($row, $postData
           
       
   

    public function save($row, $postDat
   
        if ($this->hasChildren())
            foreach ($this->getChildren() as $field)
                $field->save($row, $postData
           
       
   

    public function delete(Zend_Db_Table_Row_Abstract $ro
   
        if ($this->hasChildren())
            foreach ($this->getChildren() as $field)
                $field->delete($row
           
       
   

    public function getName
   
        if (isset($this->_properties['name']))
            return $this->getProperty('name'
        } else if (isset($this->_properties['hiddenName']))
            return $this->getHiddenName(
        } else
            return nul
       
   

    public function getFieldName
   
        $ret = $this->getName(
        if ($this->getNamePrefix())
            $ret = $this->getNamePrefix() . '_' . $re
       
        return $re
   

    public function getByName($nam
   
        if ($this->getName() == $name)
            return $thi
        } else
            return nul
       
   

    public function hasChildren
   
        return fals
   

    public function getChildren
   
        return array(
   

    public function getValidators
   
        return $this->_validator
   
    public function addValidator(Zend_Validate_Interface $
   
        $this->_validators[] = $
   

    /
     * Fügt die Standard-Validatoren für dieses Feld hinz
     * wird aufgerufen in prepareSa
    *
    protected function _addValidators
   
   

    public function getData
   
        if (!isset($this->_data))
            $this->setData(new Vps_Auto_Data_Table()
       
        return $this->_dat
   

    public function setData(Vps_Auto_Data_Interface $dat
   
        $this->_data = $dat
        $data->setFieldname($this->getName()
        return $thi
   

