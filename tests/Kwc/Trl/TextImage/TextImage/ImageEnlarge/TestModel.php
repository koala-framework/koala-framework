<?php
class Kwc_Trl_TextImage_TextImage_ImageEnlarge_TestModel extends Kwc_Abstract_Image_Model
{
    protected $_default = array(

    );
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Kwc_Trl_TextImage_TextImage_ImageEnlarge_UploadsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root-master_test-image', 'kwf_upload_id'=>1, 'dimension' => 'large'),
                    array('component_id'=>'root-master_test-image-linkTag-link', 'kwf_upload_id'=>1),
                )
            ));
        parent::__construct($config);
    }
}
