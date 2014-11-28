<?php
class Kwc_ImageResponsive_InScrollDiv_Components_Image_TestModel extends Kwc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Kwc_ImageResponsive_InScrollDiv_Components_Image_UploadsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root_image1', 'kwf_upload_id'=>3),
                    array('component_id'=>'root_image2', 'kwf_upload_id'=>3),
                )
            ));
        parent::__construct($config);
    }
}
