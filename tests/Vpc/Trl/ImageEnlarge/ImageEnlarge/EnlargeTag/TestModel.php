<?php
class Vpc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_TestModel extends Vpc_Abstract_Image_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-master_test1-linkTag', 'vps_upload_id'=>null, 'preview_image' => 0),
                array('component_id'=>'root-master_test2-linkTag', 'vps_upload_id'=>'2', 'preview_image' => 1),
                array('component_id'=>'root-master_test3-linkTag', 'vps_upload_id'=>null, 'preview_image' => 0),
                array('component_id'=>'root-master_test4-linkTag', 'vps_upload_id'=>'2', 'preview_image' => 1),
                array('component_id'=>'root-master_test5-linkTag', 'vps_upload_id'=>'2', 'preview_image' => 1),
                array('component_id'=>'root-master_test6-linkTag', 'vps_upload_id'=>'2', 'preview_image' => 1),
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
