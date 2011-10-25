<?php
class Kwc_Basic_DownloadTagBehindLogin_TestModel extends Kwc_Basic_DownloadTag_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['File']['refModelClass'] = 'Kwc_Basic_DownloadTagBehindLogin_UploadsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array('component_id', 'kwf_upload_id', 'filename'),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root_test-downloadTag', 'kwf_upload_id'=>1),
                )
            ));
        parent::__construct($config);
    }
}
