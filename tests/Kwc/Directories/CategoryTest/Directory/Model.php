<?php
class Kwc_Directories_CategoryTest_Directory_Model extends Kwf_Model_FnF
{
    protected $_toStringField = 'name';
    protected $_columns = array('id', 'name');
    protected $_data = array();
    protected $_dependentModels = array(
        'Categories' => 'Kwc_Directories_CategoryTest_Category_Directory_ItemsToCategoriesModel'
    );

    protected function _init()
    {
        parent::_init();
        for ($i=1; $i<50; $i++) {
            $this->_data[] = array(
                'id' => $i,
                'name' => 'foo'.$i,
            );
        }
    }
}
