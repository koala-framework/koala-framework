<?php
class Vps_Model_MirrorCache_SlowSource_TestModel_SlowModel extends Vps_Model_FnF
{
    protected $_columns = array('id', 'foo');
    protected $_uniqueIdentifier = 'test-mirrorcache-slowsource-src';
    public function getData()
    {
        $ret = array(
            array('id'=>1, 'foo'=>'bar'),
            array('id'=>2, 'foo'=>'bar2'),
            array('id'=>3, 'foo'=>'bar3'),
        );
        sleep(5);
        return $ret;
    }
}

class Vps_Model_MirrorCache_SlowSource_TestModel extends Vps_Model_MirrorCache
{
    protected $_maxSyncDelay = 6;
    public function __construct()
    {
        $mirrorModel = new Vps_Model_FnFFile(array(
            'uniqueIdentifier' => 'test-mirrorcache-slowsource-mirror',
            'columns' => array('id', 'foo')
        ));
        $config = array(
            'proxyModel' => $mirrorModel,
            'sourceModel' => 'Vps_Model_MirrorCache_SlowSource_TestModel_SlowModel',
            'truncateBeforeFullImport' => true,
        );
        parent::__construct($config);
    }
}
