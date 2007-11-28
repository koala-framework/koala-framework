<?p
class Vps_Auto_Field_MultiFields extends Vps_Auto_Field_Abstra

    public $field
    private $_updatedRow
    private $_deleteRow
    private $_insertedRow

    public function __construct($tableName = nul
   
        parent::__construct($tableName
        $this->fields = new Vps_Collection(
        $this->setBorder(false
        $this->setXtype('multifields'
   

    public function getMetaData
   
        $ret = parent::getMetaData(
        $ret['multiItems'] = $this->fields->getMetaData(
        if (!isset($ret['position']))
            $n = $this->getName(
            $t = new $
            $fields = $t->info(
            $ret['position'] = in_array('pos', $fields['cols']
       
        return $re
   

    public function hasChildren
   
        return sizeof($this->fields) > 
   
    public function getChildren
   
        return $this->field
   


    public function load(Zend_Db_Table_Row_Abstract $ro
   
        if ((array)$row == array()) return array(

        $ret = array($this->getFieldName()=>array()
        $rows = $row->findDependentRowset($this->getName()
        $pos = array(
        foreach ($rows as $r)
            $retRow = array(
            foreach ($this->fields as $field)
                $retRow = array_merge($retRow, $field->load($r)
           
            $ret[$this->getFieldName()][] = $retRo
            if (isset($r->pos))
                $pos[] = $r->po
           
       
        if (count($pos))
            //händisch per php sortier
            //kann verbessert werden wenn findDependentRowset ein 3. parameter ein db_select aktzeptie
            //(ist im moment noch im zend incubato
            array_multisort($pos, SORT_ASC, SORT_NUMERI
                            $ret[$this->getFieldName()]
       
        return $re
   

    public function prepareSave(Zend_Db_Table_Row_Abstract $row, $postDat
   
        $postData = Zend_Json::decode($postData[$this->getFieldName()]
        $rows = $row->findDependentRowset($this->getName()
        $this->_updatedRows = array(
        $this->_deletedRows = array(
        $this->_insertedRows = array(
        $pos = 
        foreach ($rows as $k=>$r)
            if (isset($postData[$k]))
                $rowPostData = $postData[$k
                $this->_updatedRows[] = array($r, $rowPostData
                foreach ($this->fields as $field)
                    $field->prepareSave($r, $rowPostData
               
                $pos+
                if (isset($r->pos))
                    $r->pos = $po
               
                unset($postData[$k]
            } else
                $this->_deleteRows[] = $
           
       
        foreach ($postData as $k=>$rowPostData)
            $k = (int)$
            $n = $this->getName(
            $table = new $n(
            $r = $table->createRow(
            $this->_insertedRows[] = array($r, $rowPostData
            foreach ($this->fields as $field)
                $field->prepareSave($r, $rowPostData
           
            $pos+
            if (isset($r->pos))
                $r->pos = $po
           
       
   

    public function save(Zend_Db_Table_Row_Abstract $row, $postDat
   
        foreach ($this->_insertedRows as $i)
            $r = $i[0
            $rowPostData = $i[1
            $ref = $r->getTable()->getReference(get_class($row->getTable())
            $key1 = $ref['columns'][0
            $r->$key1 = $row->i
            $r->save(
            foreach ($this->fields as $field)
                $field->save($r, $rowPostData
           
       

        foreach ($this->_updatedRows as $i)
            $r = $i[0
            $rowPostData = $i[1
            $r->save(
            foreach ($this->fields as $field)
                $field->save($r, $rowPostData
           
       

        foreach ($this->_deletedRows as $i)
            $r->delete(
       
   

