<?p
class Vps_Auto_Field_ComboBox extends Vps_Auto_Field_SimpleAbstra

    public function __construct($field_name = null, $field_label = nul
   
        parent::__construct($field_name, $field_label
        $this->setXtype('combobox'
   

    protected function _addValidators
   
        parent::_addValidators(
        $store = $this->getStore(
        if (isset($store['data']))
            $a = array(''
            foreach ($store['data'] as $r)
                $a[] = $r[0
           
            $this->addValidator(new Zend_Validate_InArray($a)
        } else if (isset($store['url']))
            //todo, keine ahnung wie 
       

   

    public function getMetaData
   
        $ret = parent::getMetaData(
        if (isset($ret[0]['storeUrl']))
            $ret[0]['store'] = array('url' => $ret[0]['storeUrl']
       
        return $re
   

    public function setValues($dat
   
        if (is_string($data))
            return $this->setStore(array('url' => $data)
        } else if ($data instanceof Vps_Db_Table_Rowset)
            $data = $data->toStringDataArray(
            return $this->setStore(array('data' => $data)
        } else if (is_array($data))
            $d = array(
            foreach ($data as $k=>$i)
                if (!is_array($i))
                    $d[] = array($k, $i
                } else
                    $d[] = $
               
           
            return $this->setStore(array('data' => $d)
       
   
    protected function _getValueFromPostData($postDat
   
        $ret = parent::_getValueFromPostData($postData
        if ($ret == '') $ret = nul
        return $re
   

