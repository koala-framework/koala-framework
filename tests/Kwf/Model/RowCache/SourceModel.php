<?php
class Vps_Model_RowCache_SourceModel extends Vps_Model_FnF
{
    protected $_data = array(
        array('id' => 1, 'foo' => 1, 'bar'=>'x1'),
        array('id' => 2, 'foo' => 2, 'bar'=>'x2'),
        array('id' => 3, 'foo' => 3, 'bar'=>'x3'),
        array('id' => 4, 'foo' => 4, 'bar'=>'x4'),
    );
    protected $_uniqueIdentifier = 'Vps_Model_RowCache_SourceModel';

    public $called = array('getRows'=>0);

    public function getRows($where = null, $order = null, $limit = null, $start = null)
    {
        $this->called['getRows']++;
        return parent::getRows($where, $order, $limit, $start);
    }
}
