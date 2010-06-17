<?php
class Vpc_Trl_ImageEnlarge_ImageEnlarge_Trl_Image_TestModel extends Vpc_Abstract_Image_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-en_test1-image', 'vps_upload_id'=>null),
                array('component_id'=>'root-en_test2-image', 'vps_upload_id'=>null),
                array('component_id'=>'root-en_test3-image', 'vps_upload_id'=>'6'),
                array('component_id'=>'root-en_test4-image', 'vps_upload_id'=>'6'),
                array('component_id'=>'root-en_test5-image', 'vps_upload_id'=>'6'),
                array('component_id'=>'root-en_test6-image', 'vps_upload_id'=>null),
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
