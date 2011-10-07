<?php
class Kwc_Basic_Image_Cache_Root_ListOwnModel extends Kwc_Abstract_List_OwnModel
{
    public function __construct($config = array())
    {
        $this->_dependentModels['Children'] = 'Kwc_Basic_Image_Cache_Root_ListModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data'=> array()
            ));
        parent::__construct($config);
    }
}
