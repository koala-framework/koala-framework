<?php
class Kwc_Composite_Images_Image_TestModel extends Kwc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Kwc_Composite_Images_Image_UploadsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'2100-1', 'kwf_upload_id'=>1),
                    array('component_id'=>'2100-2', 'kwf_upload_id'=>1),
                    array('component_id'=>'2100-3', 'kwf_upload_id'=>1),
                )
            ));
        parent::__construct($config);
    }
}
