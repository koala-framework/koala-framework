<?php
class Vpc_Table extends Vps_Db_Table
{
    protected $_primary = array('component_id', 'page_key', 'component_key');
    
    public function createDefaultRow($id, $values)
    {
        $parts = Vpc_Abstract::parseId($id);
        if ($this->find($parts['componentId'], $parts['pageKey'], $parts['componentKey'])->count() == 0) {
            $values['component_id'] = $id;
            $this->insert($values);
        }
    }

    public function find()
    {
        $componentId = 0;
        $pageKey = '';
        $componentKey = '';

        $args = func_get_args();
        if ($args[0]) {
            $parts = Vpc_Abstract::parseId($args[0]);
            $componentId = $parts['componentId'];
            $pageKey = $parts['pageKey'];
            $componentKey = $parts['componentKey'];
        }
        
        return parent::find($componentId, $pageKey, $componentKey);
    }
}