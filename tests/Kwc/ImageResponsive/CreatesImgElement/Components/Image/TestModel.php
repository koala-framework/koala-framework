<?php
class Kwc_ImageResponsive_CreatesImgElement_Components_Image_TestModel extends Kwc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Kwc_ImageResponsive_CreatesImgElement_Components_Image_UploadsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root_imageabstract1', 'kwf_upload_id'=>1),
                    array('component_id'=>'root_imageabstract2', 'dimension' => 'fullWidth', 'kwf_upload_id'=>1),
                    array('component_id'=>'root_imageabstract3', 'dimension' => 'original', 'kwf_upload_id'=>1),
                    array('component_id'=>'root_imageabstract4', 'dimension' => 'custom', 'width'=>400, 'height'=>400, 'kwf_upload_id'=>1),

                    array('component_id'=>'root_imagebasic1', 'kwf_upload_id'=>1),

                    array('component_id'=>'root_imageenlarge1', 'kwf_upload_id'=>1),

                    array('component_id'=>'root_textimage1-image', 'kwf_upload_id'=>1, 'dimension' => 'large'),
                    array('component_id'=>'root_textimage1-image-linkTag-child', 'kwf_upload_id'=>1)
                )
            ));
        parent::__construct($config);
    }
}
