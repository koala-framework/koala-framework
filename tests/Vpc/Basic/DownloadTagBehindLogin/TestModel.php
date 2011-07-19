<?php
class Vpc_Basic_DownloadTagBehindLogin_TestModel extends Vpc_Basic_DownloadTag_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['File']['refModelClass'] = 'Vpc_Basic_DownloadTagBehindLogin_UploadsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array('component_id', 'vps_upload_id', 'filename'),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root_test-downloadTag', 'vps_upload_id'=>1),
                )
            ));
        parent::__construct($config);
    }
}
