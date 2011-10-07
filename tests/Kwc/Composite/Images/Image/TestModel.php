<?php
class Vpc_Composite_Images_Image_TestModel extends Vpc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Vpc_Composite_Images_Image_UploadsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'2100-1', 'vps_upload_id'=>1),
                    array('component_id'=>'2100-2', 'vps_upload_id'=>1),
                    array('component_id'=>'2100-3', 'vps_upload_id'=>1),
                )
            ));
        parent::__construct($config);
    }
}
