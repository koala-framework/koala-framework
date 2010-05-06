<?php
abstract class Vps_Controller_Action_Auto_Filter_Abstract
{
    protected $_fieldname;

    public function __construct($config = array())
    {
        foreach ($config as $key => $val) {
            $var = '_' . $key;
            $this->$var = $val;
        }
    }

    abstract public function formatSelect($select, $query = array());
}
