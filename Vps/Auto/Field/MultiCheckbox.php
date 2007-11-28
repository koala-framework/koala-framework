<?p
//todo: validato
class Vps_Auto_Field_MultiCheckbox extends Vps_Auto_Field_Abstra

    protected $_field

    public function __construct($tableName = null, $title = nul
   
        parent::__construct(
        $this->setTableName($tableName
        if ($title) $this->setTitle($title
        $this->setHideLabels(true
        $this->setAutoHeight(true
        $this->setLayout('form'
        $this->setXtype('fieldset'
   

    public function getMetaData
   
        $ret = parent::getMetaData(
        $ret['items'] = $this->_getFields()->getMetaData(
        if (isset($ret['tableName'])) unset($ret['tableName']
        if (isset($ret['values'])) unset($ret['values']
        return $re
   

    protected function _getFields
   
        if (!isset($this->_fields))
            $this->_fields = new Vps_Collection(
            $info = $this->getValues()->getTable()->info(
            $pk = $info['primary'][1
            foreach ($this->getValues() as $i)
                $k = $i->$p
                if (!is_string($i)) $i = $i->__toString(
                $this->_fields->add(new Vps_Auto_Field_Checkbox($this->getName()."[$k]"
                    ->setKey($
                    ->setBoxLabel($i
           
       
        return $this->_field
   

    public function hasChildren
   
        return sizeof($this->_fields) > 
   
    public function getChildren
   
        return $this->_field
   

    public function getName
   
        $name = parent::getName(
        if (!$name)
            $name = $this->getTableName(
       
        return $nam
   

    public function load(Zend_Db_Table_Row_Abstract $ro
   
        if ((array)$row == array()) return array(

        $selected = $row->findDependentRowset($this->getTableName()
        $ref = $selected->getTable()->getReference(get_class($this->getValues()->getTable())
        $key = $ref['columns'][0

        $selectedIds = array(
        foreach ($selected as $i)
            $selectedIds[] = $i->$ke
       

        foreach ($this->_getFields() as $field)
            $ret[$field->getFieldName()] = in_array($field->getKey(), $selectedIds
       

        return $re
   
    public function save(Zend_Db_Table_Row_Abstract $row, $postDat
   
        $new = array(
        if ($postData[$this->getFieldName()])
            foreach($postData[$this->getFieldName()] as $key=>$value)
                if ($value) $new[] = $ke
           
       
        if ($this->getAllowBlank() === false && $new == array())
            throw new Vps_ClientException("Please select at least one ".$this->getTitle()."."
       
        $saved = $row->findDependentRowset($this->getTableName()

        $ref = $saved->getTable()->getReference(get_class($row->getTable())
        $key1 = $ref['columns'][0
      
        $ref = $saved->getTable()->getReference(get_class($this->getValues()->getTable())
        $key2 = $ref['columns'][0

        $avaliableKeys = array(
        foreach ($this->_getFields() as $field)
            $avaliableKeys[] = $field->getKey(
       

        foreach ($saved as $savedRow)
            $id = $savedRow->$key
            if (in_array($id, $avaliableKeys))
                if (!in_array($id, $new))
                    $savedRow->delete(
                    continu
                } else
                    unset($new[array_search($id, $new)]
               
           
       

        $tableName = $this->getTableName(
        $table = new $tableName(
        foreach($new as $id)
            if (in_array($id, $avaliableKeys))
                $i = $table->createRow(
                $i->$key1 = $row->i
                $i->$key2 = $i
                $i->save(
           
       
   

