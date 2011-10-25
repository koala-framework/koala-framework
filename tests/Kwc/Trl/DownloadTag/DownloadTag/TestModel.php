<?php
class Kwc_Trl_DownloadTag_DownloadTag_TestModel extends Kwc_Basic_DownloadTag_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-master_test1', 'kwf_upload_id'=>'1', 'filename'=>'foo'),
                array('component_id'=>'root-master_test2', 'kwf_upload_id'=>'1', 'filename'=>'bar'),
                array('component_id'=>'root-en_test1-download', 'kwf_upload_id'=>'2', 'filename'=>'blub'),
            )
        ));
        $this->_referenceMap['File']['refModelClass'] = 'Kwc_Trl_DownloadTag_DownloadTag_UploadsModel';
        parent::__construct($config);
    }
}
