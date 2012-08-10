<?php
class Kwc_Basic_Image_DprImage_TestModel extends Kwc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Kwc_Basic_Image_UploadsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root_page1', 'kwf_upload_id'=>2),
                    array('component_id'=>'root_page2', 'kwf_upload_id'=>1),
                )
            ));
        parent::__construct($config);
    }
}
