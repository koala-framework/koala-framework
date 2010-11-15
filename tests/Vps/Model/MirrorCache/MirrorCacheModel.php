<?php
class Vps_Model_MirrorCache_MirrorCacheModel extends Vps_Model_MirrorCache
{
    public $sourceModel;
    public $mirrorModel;
    public $siblingModel;

    public function __construct()
    {
        $this->sourceModel = new Vps_Model_FnF(array(
            'uniqueIdentifier' => 'unique',
            'columns' => array('id', 'firstname', 'timefield'),
            'uniqueColumns' => array('id'),
            'data' => array(
                array('id' => 1, 'firstname' => 'Max', 'timefield' => '2008-06-09 00:00:00'),
                array('id' => 2, 'firstname' => 'Susi', 'timefield' => '2008-07-09 10:00:00'),
                array('id' => 3, 'firstname' => 'Kurt', 'timefield' => '2008-07-15 20:00:00')
            )
        ));
        $this->mirrorModel = new Vps_Model_FnF(array(
            'uniqueIdentifier' => 'unique',
            'columns' => array('id', 'firstname', 'timefield'),
            'uniqueColumns' => array('id'),
            'data' => array(
                array('id' => 1, 'firstname' => 'Max', 'timefield' => '2008-06-09 00:00:00'),
                array('id' => 2, 'firstname' => 'Susi', 'timefield' => '2008-07-09 10:00:00')
            )
        ));
        $this->siblingModel = new Vps_Model_MirrorCache_SiblingModel(array(
            'uniqueIdentifier' => 'unique',
            'columns' => array('id', 'siblingcol'),
            'uniqueColumns' => array('id'),
            'data' => array(
                array('id' => 1, 'siblingcol' => 'sib1'),
                array('id' => 2, 'siblingcol' => 'sib2')
            )
        ));

        $config = array(
            'proxyModel' => $this->mirrorModel,
            'sourceModel' => $this->sourceModel,
            'siblingModels' => array($this->siblingModel),
            'syncTimeField' => 'timefield',
            'maxSyncDelay' => 2
        );
        parent::__construct($config);
    }
}
