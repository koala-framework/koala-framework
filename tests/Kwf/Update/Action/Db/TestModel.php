<?php
class Vps_Update_Action_Db_TestModel extends Vps_Model_FnF
{
    protected $_columns = array('table', 'data');
    protected $_primaryKey = 'table';
    public function __construct(array $options = array())
    {
        $this->_data = array(
            array('table'=>'foo', 'data'=>serialize(array(
            'autoId' => 0,
            'data' => array(
                array('field'=>'id', 'type'=>'int'),
                array('field'=>'bar', 'type'=>'text', 'default'=>5),
            ))))
        );
        $this->_dependentModels = array(
            'Fields' => new Vps_Model_FieldRows(array(
                'primaryKey' => 'field',
                'columns' => array('field', 'type', 'null', 'key', 'default', 'extra'),
                'fieldName' => 'data'
            ))
        );
        parent::__construct($options);
    }
}
