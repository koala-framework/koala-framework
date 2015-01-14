<?php
class Kwc_Basic_Text_Image_TestModel extends Kwc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Kwc_Basic_Text_Image_UploadsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'columns' => array(),
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'1011-i1', 'kwf_upload_id'=>'c1f100f2-8967-4d03-8773-dbe3b43f3955'),
                array('component_id'=>'1015-i1', 'kwf_upload_id'=>'c1f100f2-8967-4d03-8773-dbe3b43f3955')
            )
        ));
        parent::__construct($config);
    }
}
