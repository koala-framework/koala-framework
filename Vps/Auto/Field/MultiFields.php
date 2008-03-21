<?php
class Vps_Auto_Field_MultiFields extends Vps_Auto_Field_Abstract
{
    public $fields;
    private $_updatedRows;
    private $_deleteRows;
    private $_insertedRows;

    public function __construct($tableName = null)
    {
        parent::__construct($tableName);
        $this->fields = new Vps_Collection();
        $this->setBorder(false);
        $this->setXtype('multifields');
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        $ret['multiItems'] = $this->fields->getMetaData();
        if (!isset($ret['position'])) {
            $n = $this->getName();
            $t = new $n;
            $fields = $t->info();
            $ret['position'] = in_array('pos', $fields['cols']);
        }
        return $ret;
    }

    public function hasChildren()
    {
        return sizeof($this->fields) > 0;
    }
    public function getChildren()
    {
        return $this->fields;
    }


    public function load(Zend_Db_Table_Row_Abstract $row)
    {
        if ((array)$row == array()) return array();

        $ret = array($this->getFieldName()=>array());
        $rows = $row->findDependentRowset($this->getName());
        $pos = array();
        foreach ($rows as $r) {
            $retRow = array();
            foreach ($this->fields as $field) {
                $retRow = array_merge($retRow, $field->load($r));
            }
            $ret[$this->getFieldName()][] = $retRow;
            if (isset($r->pos)) {
                $pos[] = $r->pos;
            }
        }
        if (count($pos)) {
            //h�ndisch per php sortieren
            //kann verbessert werden wenn findDependentRowset ein 3. parameter ein db_select aktzeptiert
            //(ist im moment noch im zend incubator)
            array_multisort($pos, SORT_ASC, SORT_NUMERIC,
                            $ret[$this->getFieldName()]);
        }
        return $ret;
    }

    public function prepareSave(Zend_Db_Table_Row_Abstract $row, $postData)
    {
        $postData = Zend_Json::decode($postData[$this->getFieldName()]);
        $rows = $row->findDependentRowset($this->getName());
        $this->_updatedRows = array();
        $this->_deletedRows = array();
        $this->_insertedRows = array();
        $pos = 0;
        foreach ($rows as $k=>$r) {
            if (isset($postData[$k])) {
                $rowPostData = $postData[$k];
                $this->_updatedRows[] = array($r, $rowPostData);
                foreach ($this->fields as $field) {
                    $field->prepareSave($r, $rowPostData);
                }
                $pos++;
                if (isset($r->pos)) {
                    $r->pos = $pos;
                }
                unset($postData[$k]);
            } else {
                $this->_deletedRows[] = $r;
            }
        }
        foreach ($postData as $k=>$rowPostData) {
            $k = (int)$k;
            $n = $this->getName();
            $table = new $n();
            $r = $table->createRow();
            $this->_insertedRows[] = array($r, $rowPostData);
            foreach ($this->fields as $field) {
                $field->prepareSave($r, $rowPostData);
            }
            $pos++;
            if (isset($r->pos)) {
                $r->pos = $pos;
            }
        }
    }

    public function save(Zend_Db_Table_Row_Abstract $row, $postData)
    {
        foreach ($this->_deletedRows as $r) {
            $r->delete();
        }

        foreach ($this->_insertedRows as $i) {
            $r = $i[0];
            $rowPostData = $i[1];
            $ref = $r->getTable()->getReference(get_class($row->getTable()));
            $key1 = $ref['columns'][0];
            $r->$key1 = $row->id;
            $r->save();
            foreach ($this->fields as $field) {
                $field->save($r, $rowPostData);
            }
        }

        foreach ($this->_updatedRows as $i) {
            $r = $i[0];
            $rowPostData = $i[1];
            $r->save();
            foreach ($this->fields as $field) {
                $field->save($r, $rowPostData);
            }
        }

    }
}
