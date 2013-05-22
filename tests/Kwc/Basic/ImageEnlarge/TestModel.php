<?php
class Kwc_Basic_ImageEnlarge_TestModel extends Kwc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Kwc_Basic_ImageEnlarge_UploadsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'1800', 'kwf_upload_id'=>1),
                    array('component_id'=>'1801', 'kwf_upload_id'=>1),
                    array('component_id'=>'1802', 'kwf_upload_id'=>2),
                    array('component_id'=>'1802-linkTag', 'kwf_upload_id'=>1, 'preview_image'=>1),
                    array('component_id'=>'1803', 'kwf_upload_id'=>1),
                    array('component_id'=>'1804', 'kwf_upload_id'=>4),
                )
            ));
        parent::__construct($config);
    }
}
