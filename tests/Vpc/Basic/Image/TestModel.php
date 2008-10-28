<?php
class Vpc_Basic_Image_TestModel extends Vpc_Basic_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Vpc_Basic_Image_UploadsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array('component_id', 'width', 'height', 'scale', 'vps_upload_id', 'filename', 'enlarge', 'comment'),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>1600, 'vps_upload_id'=>1),
                    array('component_id'=>1601, 'vps_upload_id'=>1, 'filename'=>'myname'),
                    array('component_id'=>1603, 'vps_upload_id'=>1, 'width'=>10, 'height'=>10, 'scale'=>Vps_Media_Image::SCALE_DEFORM),
                    array('component_id'=>1605, 'vps_upload_id'=>1),
                )
            ));
        parent::__construct($config);
    }
}
