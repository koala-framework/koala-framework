<?php
class Vpc_Trl_ImageEnlarge_ImageEnlarge_TestModel extends Vpc_Abstract_Image_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-master_test1', 'vps_upload_id'=>'1'),
                array('component_id'=>'root-master_test2', 'vps_upload_id'=>'1'),
                array('component_id'=>'root-master_test3', 'vps_upload_id'=>'1'),
                array('component_id'=>'root-master_test4', 'vps_upload_id'=>'1'),
                array('component_id'=>'root-master_test5', 'vps_upload_id'=>'1'),
                array('component_id'=>'root-master_test6', 'vps_upload_id'=>'1'),
            )
        ));
        $this->_referenceMap['Image']['refModelClass'] = 'Vpc_Trl_ImageEnlarge_UploadsModel';
        parent::__construct($config);
    }


    protected function _init()
    {
        parent::_init();
        $this->_siblingModels = array();
    }
}
