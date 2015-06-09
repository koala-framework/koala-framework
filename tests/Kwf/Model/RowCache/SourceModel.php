<?php
class Kwf_Model_RowCache_SourceModel extends Kwf_Model_FnFFile
{
    protected $_uniqueIdentifier = 'Kwf_Model_RowCache_SourceModel';

    public $called = array('getRows'=>0);

    public function getRows($where = null, $order = null, $limit = null, $start = null)
    {
        $this->called['getRows']++;
        return parent::getRows($where, $order, $limit, $start);
    }
}
