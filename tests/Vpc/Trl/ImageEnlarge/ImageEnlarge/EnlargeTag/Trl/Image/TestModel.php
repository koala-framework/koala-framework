<?php
class Vpc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_Image_TestModel extends Vpc_Abstract_Image_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-en_test1-linkTag-image', 'vps_upload_id'=>null),
                array('component_id'=>'root-en_test2-linkTag-image', 'vps_upload_id'=>null),
                array('component_id'=>'root-en_test3-linkTag-image', 'vps_upload_id'=>null),
                array('component_id'=>'root-en_test4-linkTag-image', 'vps_upload_id'=>null),
                array('component_id'=>'root-en_test5-linkTag-image', 'vps_upload_id'=>'5'),
                array('component_id'=>'root-en_test6-linkTag-image', 'vps_upload_id'=>'5'),
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
