<?php
class Kwc_List_GalleryComposite_ImageEnlarge_TestModel extends Kwc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Kwc_List_GalleryComposite_ImageEnlarge_UploadsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array(),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root_page1-1-imageEnlarge', 'kwf_upload_id'=>1),
                    array('component_id'=>'root_page1-2-imageEnlarge', 'kwf_upload_id'=>1),
                    array('component_id'=>'root_page1-3-imageEnlarge', 'kwf_upload_id'=>1),
                )
            ));
        parent::__construct($config);
    }
}
