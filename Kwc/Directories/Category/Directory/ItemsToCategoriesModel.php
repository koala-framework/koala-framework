<?php
abstract class Kwc_Directories_Category_Directory_ItemsToCategoriesModel
    extends Kwf_Model_Db_Proxy
{
    protected $_referenceMap = array(
        'Category' => array(
            'column'        => 'category_id',
            'refModelClass' => 'Kwc_Directories_Category_Directory_CategoriesModel'
        ),
        'Item' => array()
    );

    protected function _init()
    {
        parent::_init();
        if (!count($this->_referenceMap['Item'])) {
            throw new Kwf_Exception("Reference 'Item' must be set in model '".get_class($this)."'");
        }
    }

}
