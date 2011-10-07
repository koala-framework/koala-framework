<?php
class Vpc_Composite_ImagesEnlarge_ImageEnlarge_TestModel extends Vpc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Vpc_Composite_ImagesEnlarge_ImageEnlarge_UploadsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'2200-1', 'vps_upload_id'=>1),
                    array('component_id'=>'2200-2', 'vps_upload_id'=>1),
                    array('component_id'=>'2200-3', 'vps_upload_id'=>1),
                )
            ));
        parent::__construct($config);
    }
}
