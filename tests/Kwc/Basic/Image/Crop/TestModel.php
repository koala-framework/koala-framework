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
                    array('component_id'=>'root_page', 'kwf_upload_id'=>1
                        ,'crop_x' => 14, 'crop_y' => 820, 'crop_width' => 2012, 'crop_height' => 450),
                    array('component_id'=>'root_page1', 'kwf_upload_id'=>1
                        ,'crop_x' => 14, 'crop_y' => 820, 'crop_width' => 2012, 'crop_height' => 450),
                    array('component_id'=>'root_page2', 'kwf_upload_id'=>1, 'width' => 100, 'height' => 100
                        ,'crop_x' => 14, 'crop_y' => 820, 'crop_width' => 2012, 'crop_height' => 450),
                    array('component_id'=>'root_page10', 'kwf_upload_id'=>1, 'width' => 100, 'height' => 100
                        ,'crop_x' => 14, 'crop_y' => 820, 'crop_width' => 2012, 'crop_height' => 450),

                    array('component_id'=>'root_page3', 'kwf_upload_id'=>1, 'dimension' => 'small'
                        ,'crop_x' => 14, 'crop_y' => 820, 'crop_width' => 2012, 'crop_height' => 450),
                    array('component_id'=>'root_page4', 'kwf_upload_id'=>1, 'dimension' => 'medium'
                        ,'crop_x' => 14, 'crop_y' => 820, 'crop_width' => 2012, 'crop_height' => 450),
                    array('component_id'=>'root_page5', 'kwf_upload_id'=>1, 'dimension' => 'original'
                        ,'crop_x' => 14, 'crop_y' => 820, 'crop_width' => 2012, 'crop_height' => 450),
                    array('component_id'=>'root_page6', 'kwf_upload_id'=>1, 'dimension' => 'userWidth', 'width'=>50
                        ,'crop_x' => 14, 'crop_y' => 820, 'crop_width' => 2012, 'crop_height' => 450),
                    array('component_id'=>'root_page7', 'kwf_upload_id'=>1, 'dimension' => 'userHeight', 'height'=>50
                        ,'crop_x' => 14, 'crop_y' => 820, 'crop_width' => 2012, 'crop_height' => 450),
                    array('component_id'=>'root_page8', 'kwf_upload_id'=>1, 'dimension' => 'userSize', 'width'=>50, 'height'=>50
                        ,'crop_x' => 14, 'crop_y' => 820, 'crop_width' => 2012, 'crop_height' => 450),
                    array('component_id'=>'root_page9', 'kwf_upload_id'=>1, 'dimension' => 'userWidth', 'width'=>null
                        ,'crop_x' => 14, 'crop_y' => 820, 'crop_width' => 2012, 'crop_height' => 450)
                )
            ));
        parent::__construct($config);
    }
}
