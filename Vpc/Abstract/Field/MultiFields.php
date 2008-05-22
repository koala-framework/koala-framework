<?php
class Vpc_Abstract_Field_MultiFields extends Vps_Form_Field_MultiFields
{
    public function __construct($class)
    {
        $table = Vpc_Abstract::createTable($class);
        parent::__construct($table);
        // das sollte hier nicht fix kodiert werden
        $this->setReferences(array(
            'columns' => array('component_id'),
            'refColumns' => array('id')
        ));
        $this->_class = $class;
    }
    
    // gehört nicht hier rein, ist jetzt nur für Vpc_Abstract_List_Form - und funktionieren tuts auch nicht?
    public function prepareSave($row, $postData)
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        $row->component_class = $classes['child'];
        parent::prepareSave($row, $postData);
    }
}
