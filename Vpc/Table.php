<?php
abstract class Vpc_Table extends Vps_Db_Table
{
    protected $_primary = array('component_id', 'page_key', 'component_key');
    
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

    public function find()
    {
        $componentId = 0;
        $pageKey = '';
        $componentKey = '';

        $args = func_get_args();
        if (sizeof($args) == 1) {
            $parts = Vpc_Abstract::parseId($args[0]);
            $componentId = $parts['componentId'];
            $pageKey = $parts['pageKey'];
            $componentKey = $parts['componentKey'];
        } else if (sizeof($args) == 3) {
            $componentId = $args[0];
            $pageKey = $args[1];
            $componentKey = $args[2];
        } else {
            throw new Vps_Exception("You must call find with one (complete componentId) or three (componentId, pageKey, componentKey) arguments.");
        }
        
        return parent::find($componentId, $pageKey, $componentKey);
    }
}
    