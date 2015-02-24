<?php
class Kwc_Basic_DownloadTag_TestModel extends Kwc_Basic_DownloadTag_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['File']['refModelClass'] = 'Kwc_Basic_DownloadTag_UploadsModel';

        $m = Kwf_Model_Abstract::getInstance('Kwc_Basic_DownloadTag_UploadsModel');
        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array('component_id', 'kwf_upload_id', 'filename'),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>1700, 'kwf_upload_id'=>$m->uploadId1),
                    array('component_id'=>1701, 'kwf_upload_id'=>$m->uploadId1, 'filename'=>'myname')
                )
            ));
        parent::__construct($config);
    }
}
