<?php
class Kwc_Abstract_Image_TestModel extends Kwc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Kwc_Abstract_Image_UploadsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root_imageabstract1', 'kwf_upload_id'=>1, 'dimension' => 'default'),
                    array('component_id'=>'root_imageabstract2', 'kwf_upload_id'=>3, 'dimension' => 'default2')
                )
            ));
        parent::__construct($config);
    }
}
