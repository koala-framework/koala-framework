<?php
abstract class Vpc_Table extends Vps_Db_Table
{
    protected $_primary = array('page_id', 'component_key');
    
    public function createDefaultRow($key, $values)
    {
        if ($this->find($key)->count() == 0) {
            $parts = Vpc_Abstract::parseId($key);
            $values['component_id'] = $parts['componentId'];
            $values['page_key'] = $parts['pageKey'];
            $values['component_key'] = $parts['componentKey'];
            $this->insert($values);
        }
    }

}
    