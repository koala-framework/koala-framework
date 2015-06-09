<?php
class Kwc_Basic_Text_Download_TestModel extends Kwc_Basic_DownloadTag_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['File']['refModelClass'] = 'Kwc_Basic_Text_Download_UploadsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'columns' => array('component_id', 'kwf_upload_id', 'filename'),
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'1013-d1', 'kwf_upload_id'=>'b7715975-0252-4d31-ae9c-589a5f11620a')
            )
        ));
        parent::__construct($config);
    }
}
