<?php
class Vpc_TextImage_ImageEnlarge_TestModel extends Vpc_Abstract_Image_Model
{
    protected $_default = array(

    );
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Vpc_TextImage_ImageEnlarge_UploadsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root_textImage1-image', 'vps_upload_id'=>1, 'dimension' => 'large'),
                    array('component_id'=>'root_textImage1-image-linkTag-child', 'vps_upload_id'=>1),
                )
            ));
        parent::__construct($config);
    }
}
