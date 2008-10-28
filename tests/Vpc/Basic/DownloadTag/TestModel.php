<?php
class Vpc_Basic_DownloadTag_TestModel extends Vpc_Basic_DownloadTag_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['File']['refModelClass'] = 'Vpc_Basic_DownloadTag_UploadsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array('component_id', 'vps_upload_id', 'filename'),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>1700, 'vps_upload_id'=>1),
                    array('component_id'=>1701, 'vps_upload_id'=>1, 'filename'=>'myname')
                )
            ));
        parent::__construct($config);
    }
}
