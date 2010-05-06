<?php
class Vps_Controller_Action_Auto_Filter_Query extends Vps_Controller_Action_Auto_Filter_Abstract
{
    protected $_fieldname;

    public function __construct($config = array())
    {
        if (!isset($config['fieldname'])) throw new Vps_Exception('Parameter "fieldname" ist needed for Query-Filter');
        parent::__construct($config);
    }

    public function formatSelect($select, $params = array()) {
        if (isset($params['query']) && $params['query']) {
            $select->whereEquals($this->_fieldname, $params['query']);
        }
        return $select;
    }
}
