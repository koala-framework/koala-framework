<?php
class Vpc_Basic_Image_TestModel extends Vpc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Vpc_Basic_Image_UploadsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>1600, 'vps_upload_id'=>1),
                    array('component_id'=>1601, 'vps_upload_id'=>1, 'filename'=>'myname'),
                    array('component_id'=>1603, 'vps_upload_id'=>1, 'width'=>10, 'height'=>10, 'scale'=>Vps_Media_Image::SCALE_DEFORM),
                    array('component_id'=>1605, 'vps_upload_id'=>1),
                    array('component_id'=>1606, 'vps_upload_id'=>1),
                    array('component_id'=>1607, 'vps_upload_id'=>1, 'dimension' => 'small'),
                    array('component_id'=>1608, 'vps_upload_id'=>1, 'dimension' => 'medium'),
                    array('component_id'=>1609, 'vps_upload_id'=>1, 'dimension' => 'original'),
                    array('component_id'=>1610, 'vps_upload_id'=>1, 'dimension' => 'userWidth', 'width'=>50),
                    array('component_id'=>1611, 'vps_upload_id'=>1, 'dimension' => 'userHeight', 'height'=>50),
                    array('component_id'=>1612, 'vps_upload_id'=>1, 'dimension' => 'userSize', 'width'=>50, 'height'=>50),
                    array('component_id'=>1614, 'vps_upload_id'=>1, 'dimension' => 'userWidth', 'width'=>null),
                    array('component_id'=>1615, 'vps_upload_id'=>1, 'dimension' => 'userSizeScale', 'width'=>null, 'height'=>null, 'scale'=>null),
                    array('component_id'=>1616, 'vps_upload_id'=>1, 'dimension' => null),
                )
            ));
        parent::__construct($config);
    }
}
