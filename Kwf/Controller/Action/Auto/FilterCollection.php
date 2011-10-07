<?php
class Vps_Controller_Action_Auto_FilterCollection extends Vps_Collection
{
    public function offsetSet($offset, $value)
    {
        if ($offset) {
            $this->add($this->_getFilterFromArray($offset, $value));
        } else {
            parent::offsetSet($offset, $value);
        }
    }

    private function _getFilterFromArray($field, $config)
    {
        if ($field == 'text' && is_bool($config) && $config) {
            $type = 'text';
        } else if (isset($config['type'])) {
            $type = $config['type'];
            unset($config['type']);
        } else {
            throw new Vps_Exception('No filter-type defined');
        }
        if (strtolower($type) == 'textfield') $type = 'text';
        $class = 'Vps_Controller_Action_Auto_Filter_' . ucfirst($type);
        if (!class_exists($class)) throw new Vps_Exception('Unknown Filter: ' . $type);
        $filter = new $class();
        if (!is_array($config)) $config = array();
        $config['fieldName'] = $field;
        foreach ($config as $key => $val) $filter->setProperty($key, $val);
        return $filter;
    }
}