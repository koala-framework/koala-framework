<?php
class Kwc_Basic_Image_Crop_TestModel extends Kwc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Kwc_Basic_Image_Crop_UploadsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root_page', 'kwf_upload_id'=>1,
                        'crop_x' => 50, 'crop_y' => 50, 'crop_width' => 50, 'crop_height' => 50)
                )
            ));
        parent::__construct($config);
    }
}
