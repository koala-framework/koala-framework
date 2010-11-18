<?php
class Vpc_Trl_DownloadTag_DownloadTag_TestModel extends Vpc_Basic_DownloadTag_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-master_test1', 'vps_upload_id'=>'1', 'filename'=>'foo'),
                array('component_id'=>'root-master_test2', 'vps_upload_id'=>'1', 'filename'=>'bar'),
                array('component_id'=>'root-en_test1-download', 'vps_upload_id'=>'2', 'filename'=>'blub'),
            )
        ));
        $this->_referenceMap['File']['refModelClass'] = 'Vpc_Trl_DownloadTag_DownloadTag_UploadsModel';
        parent::__construct($config);
    }
}
