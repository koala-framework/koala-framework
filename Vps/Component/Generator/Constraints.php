<?php
class Vps_Component_Generator_Constraints
{
    private $_used = array();
    private $_parts = array();
    
    public function __construct($where = array())
    {
        foreach ($where as $key => $val) {
            $method = "where$key";
            $this->$method($val);
        }
    }
    
    public function __call($method, $arguments)
    {
        if (substr($method, 0, 5) == 'where') {
            $what = strtolower(substr($method, 6));
            $this->_parts[$what] = $arguments[0];
            return $this;
        } else {
            if (isset($this->_parts[$what])) {
                $this->_used[] = $what;
                return $this->_parts[$what];
            } else {
                return null;
            }
        }
    }
}