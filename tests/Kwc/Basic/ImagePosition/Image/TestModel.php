<?php
class Vpc_Basic_ImagePosition_Image_TestModel extends Vpc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Vpc_Basic_ImagePosition_Image_UploadsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'1900-image', 'vps_upload_id'=>1),
                )
            ));
        parent::__construct($config);
    }
}
