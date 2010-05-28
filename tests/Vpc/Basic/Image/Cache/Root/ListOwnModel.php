<?php
class Vpc_Basic_Image_Cache_Root_ListOwnModel extends Vpc_Abstract_List_OwnModel
{
    public function __construct($config = array())
    {
        $this->_dependentModels['Children'] = 'Vpc_Basic_Image_Cache_Root_ListModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data'=> array()
            ));
        parent::__construct($config);
    }
}
