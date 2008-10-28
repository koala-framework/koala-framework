<?php
class Vpc_Basic_ImageEnlarge_TestModel extends Vpc_Basic_Image_Enlarge_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Vpc_Basic_ImageEnlarge_UploadsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array('component_id', 'width', 'height', 'scale', 'vps_upload_id', 'filename', 'enlarge', 'comment'),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>1800, 'vps_upload_id'=>1),
                    array('component_id'=>1801, 'vps_upload_id'=>1),
                    array('component_id'=>1802, 'vps_upload_id'=>1),
                    array('component_id'=>'1802-smallImage', 'vps_upload_id'=>2),
                    array('component_id'=>1803, 'vps_upload_id'=>1),
                )
            ));
        parent::__construct($config);
    }
}
