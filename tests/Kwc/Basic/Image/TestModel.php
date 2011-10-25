<?php
class Kwc_Basic_Image_TestModel extends Kwc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Kwc_Basic_Image_UploadsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>1600, 'kwf_upload_id'=>1),
                    array('component_id'=>1601, 'kwf_upload_id'=>1, 'filename'=>'myname'),
                    array('component_id'=>1603, 'kwf_upload_id'=>1, 'width'=>10, 'height'=>10, 'scale'=>Kwf_Media_Image::SCALE_DEFORM),
                    array('component_id'=>1605, 'kwf_upload_id'=>1),
                    array('component_id'=>1606, 'kwf_upload_id'=>1),
                    array('component_id'=>1607, 'kwf_upload_id'=>1, 'dimension' => 'small'),
                    array('component_id'=>1608, 'kwf_upload_id'=>1, 'dimension' => 'medium'),
                    array('component_id'=>1609, 'kwf_upload_id'=>1, 'dimension' => 'original'),
                    array('component_id'=>1610, 'kwf_upload_id'=>1, 'dimension' => 'userWidth', 'width'=>50),
                    array('component_id'=>1611, 'kwf_upload_id'=>1, 'dimension' => 'userHeight', 'height'=>50),
                    array('component_id'=>1612, 'kwf_upload_id'=>1, 'dimension' => 'userSize', 'width'=>50, 'height'=>50),
                    array('component_id'=>1614, 'kwf_upload_id'=>1, 'dimension' => 'userWidth', 'width'=>null),
                    array('component_id'=>1615, 'kwf_upload_id'=>1, 'dimension' => 'userSizeScale', 'width'=>null, 'height'=>null, 'scale'=>null),
                    array('component_id'=>1616, 'kwf_upload_id'=>1, 'dimension' => null),
                )
            ));
        parent::__construct($config);
    }
}
