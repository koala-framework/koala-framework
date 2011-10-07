<?php
class Kwc_Trl_DownloadTag_DownloadTag_Trl_TestModel extends Kwf_Model_FnF
{
    public function __construct()
    {
        $config = array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-en_test1', 'own_download'=>1),
                array('component_id'=>'root-en_test2', 'own_download'=>0),
            )
        );
        parent::__construct($config);
    }
}
