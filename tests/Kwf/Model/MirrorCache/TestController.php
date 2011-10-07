<?php
class Kwf_Model_MirrorCache_TestController extends Kwf_Controller_Action
{
    public static $sourceModel;
    public static $mirrorModel;
    public static $proxyModel;

    static function setup()
    {
        self::$sourceModel = new Kwf_Model_FnF(array(
            'uniqueIdentifier' => 'unique',
            'columns' => array('id', 'firstname', 'lastname', 'timefield'),
            'uniqueColumns' => array('id'),
            'data' => array(
                array('id' => 1, 'firstname' => 'Max', 'timefield' => '2008-06-09 00:00:00'),
                array('id' => 2, 'firstname' => 'Susi', 'timefield' => '2008-07-09 10:00:00'),
                array('id' => 3, 'firstname' => 'Kurt', 'timefield' => '2008-07-15 20:00:00')
            )
        ));
        self::$mirrorModel = new Kwf_Model_FnF(array(
            'uniqueIdentifier' => 'unique',
            'columns' => array('id', 'firstname', 'lastname', 'timefield'),
            'uniqueColumns' => array('id'),
            'data' => array(
                array('id' => 1, 'firstname' => 'Max', 'timefield' => '2008-06-09 00:00:00'),
                array('id' => 2, 'firstname' => 'Susi', 'timefield' => '2008-07-09 10:00:00')
            )
        ));

        self::$proxyModel =  new Kwf_Model_MirrorCache(array(
            'proxyModel' => self::$mirrorModel,
            'sourceModel' => self::$sourceModel,
            'syncTimeField' => 'timefield',
            'maxSyncDelay' => 5
        ));
    }

    public function indexAction()
    {
        Kwf_Benchmark::enable();
        Kwf_Benchmark::reset();

        self::setup();

        self::$proxyModel->countRows();
        echo (int)Kwf_Benchmark::getCounterValue('mirror sync');
        exit;
    }
}
