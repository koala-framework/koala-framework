<?php
class Vps_Auto_Vpc_Form extends Vps_Auto_Form
{
    public function __construct($class, $pageId = null, $componentKey = null)
    {
        $tablename = Vpc_Abstract::getSetting($class, 'tablename');
        if ($tablename) {
            $this->setTable(new $tablename(array('componentClass'=>$class)));
        } else {
            throw new Vpc_Exception('No tablename in Setting defined: ' . $class);
        }

        $table = $this->getTable();
        if ($pageId) {
            $info = $table->info();
            if (sizeof($info['primary']) == 1) {
                $this->_row = $table->find($pageId)->current();
                $id = array('page_id' => $pageId);
            } else {
                $this->_row = $table->find($pageId, $componentKey)->current();
                $id = array(
                    'page_id' => $pageId,
                    'component_key' => $componentKey
                );
            }
            if (!$this->_row) {
                $this->_row = $table->createRow($id);
            }
        } else {
            $this->_row = $table->createRow();
            $id = 0;
        }

        parent::__construct($class, $id);
    }
}
