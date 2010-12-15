<?php
abstract class Vpc_Directories_Category_Directory_ItemsToCategoriesModel
    extends Vps_Model_Db_Proxy
{
    protected $_referenceMap = array(
        'Category' => array(
            'column'        => 'category_id',
            'refModelClass' => 'Vpc_Directories_Category_Directory_CategoriesModel'
        ),
        'Item' => array()
    );

    protected function _init()
    {
        parent::_init();
        if (!count($this->_referenceMap['Item'])) {
            throw new Vps_Exception("Reference 'Item' must be set in model '".get_class($this)."'");
        }
    }

}
