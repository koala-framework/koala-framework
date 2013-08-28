<?php
class Kwf_Model_MirrorCache_LockRecursion_Model extends Kwf_Model_MirrorCache
{
    public function __construct()
    {
        $config = array();
        $config['sourceModel'] = new Kwf_Model_FnF(array(
            'uniqueIdentifier' => 'unique',
            'columns' => array('id', 'firstname'),
            'uniqueColumns' => array('id'),
            'data' => array(
                array('id' => 1, 'firstname' => 'Max'),
                array('id' => 2, 'firstname' => 'Susi')
            )
        ));
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'uniqueIdentifier' => 'unique',
            'columns' => array('id', 'firstname'),
            'uniqueColumns' => array('id'),
            'data' => array(
            )
        ));
        $config['maxSyncDelay'] = 1;
        parent::__construct($config);
    }

    protected function _afterImport($format, $data, $options)
    {
        parent::_afterImport($format, $data, $options);

        //read after every sync
        $this->getIds(array());

        //one time re-sync (only one time else endless loop obviously)
        static $done = false;
        if (!$done) {
            $done = true;
            $this->synchronize(self::SYNC_ALWAYS);
        }
    }
}
