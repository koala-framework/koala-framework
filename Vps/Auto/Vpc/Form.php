<?php
class Vps_Auto_Vpc_Form extends Vps_Auto_Form
{
    public function __construct($class, $id = null)
    {
        parent::__construct($class, $id);
    }

    public function setId($id)
    {
        $class = $this->getName();

        $tablename = Vpc_Abstract::getSetting($class, 'tablename');
        if ($tablename) {
            $this->setTable(new $tablename(array('componentClass'=>$class)));
        } else {
            throw new Vpc_Exception('No tablename in Setting defined: ' . $class);
        }

        $table = $this->getTable();
        
        if (isset($id['page_id']) && $id['page_id']!==null) {
            $info = $table->info();
            if (sizeof($info['primary']) == 1) {
                $this->_row = $table->find($id['page_id'])->current();
                $id = array('page_id' => $id['page_id']);
            } else {
                $this->_row = $table->find($id['page_id'], $id['component_key'])->current();
                $id = array(
                    'page_id' => $id['page_id'],
                    'component_key' => $id['component_key']
                );
            }
            if (!$this->_row) {
                $this->_row = $table->createRow($id);
            }
        } else {
            $id = 0;
            $this->_row = $table->createRow();
        }
        parent::setId($id);
    }
}
