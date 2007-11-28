<?p
class Vps_Collection implements ArrayAccess, IteratorAggrega

    private $_array = array(

    private $_defaultClas

    /
     * @param string Wenn angegeben kann diese Collection nur Klassen von diesem T
     *               beinhalten. Falls kein Objekt hinzugefügt wird, so wird e
     *               Objekt von der hier angegeben Klasse intanzier
     *
    public function __construct($defaultClass = nul
   
        $this->_defaultClass = $defaultClas
   

    //ArrayAcce
    public function offsetExists($offse
   
        foreach ($this->_array as $v)
            if ($ret = $v->getByName($name))
                return tru
           
       
        return fals
   

    public function getByName($nam
   
        foreach ($this->_array as $v)
            if ($ret = $v->getByName($name))
                return $re
           
       
   

    //ArrayAcce
    public function offsetGet($offse
   
        return $this->getByName($offset
   

    //ArrayAcce
    public function offsetSet($offset, $valu
   
        if (is_null($offset))
            $this->add($value
        } else
            throw new Vps_Exception("Not yet Implemented."
       
   

    public function add($value = nul
   
        $value = $this->_preInsertValue($value
        $this->_array[] = $valu
        $this->_postInsertValue($value
        return $valu
    
   
    //ArrayAcce
    public function offsetUnset($offse
   
        foreach ($this->_array as $k=>$v)
            if ($ret = $v->getByName($offset))
                unset($this->_array[$k]
                retur
           
       
        throw new Vps_Exception("Offset '$offset' not found"
   

    //IteratorAggrega
    public function getIterator
   
        return new Vps_Collection_Iterator($this
   

    public function getRecursiveIterator
   
        return new RecursiveIteratorIterato
                        new Vps_Collection_Iterator_Recursive($this)
   

    public function first
   
        if (isset($this->_array[0]))
            return $this->_array[0
        } else
            return nul
       
   

    public function prepend($valu
   
        $value = $this->_preInsertValue($value
        array_unshift($this->_array, $value
        $this->_postInsertValue($value
        return $valu
   

    public function append($valu
   
        return $this->add($value
   

    public function insertBefore($where, Vps_Collection_Item_Interface $valu
   
        $value = $this->_preInsertValue($value
        foreach ($this->_array as $i=>$v)
            if ($v->getName() == $where)
                array_splice($this->_array, $i, 0, array($value)
           
       
        $this->_postInsertValue($value
        return $valu
   

    public function getArray
   
        return $this->_arra
   

    protected function _postInsertValue($valu
   
   
    protected function _preInsertValue($valu
   
        if ($this->_defaultClass && !is_object($value))
            $value = new $this->_defaultClass($value
       
        if (!$value instanceof Vps_Collection_Item_Interface)
            throw new Vps_Exception("Vps_Collection can hold only items with Vps_Collection_Item_Interface"
       
        return $valu
   

    public function getMetaData
   
        $ret = array(
        foreach ($this as $field)
            $data = $field->getMetaData(
            if (!is_null($data)) $ret[] = $dat
       
        return $re
   

