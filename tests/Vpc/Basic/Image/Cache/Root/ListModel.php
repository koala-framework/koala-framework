<?php
class Vpc_Basic_Image_Cache_Root_ListModel extends Vpc_Abstract_List_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Component']['refModelClass'] = 'Vpc_Basic_Image_Cache_Root_ListOwnModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
            'columns' => array('id', 'component_id', 'pos', 'visible', 'data'),
            'primaryKey' => 'id',
            'siblingModels' => array(
                new Vps_Model_Field(array('fieldName'=>'data'))
            )
        ));

        parent::__construct($config);
    }
}
