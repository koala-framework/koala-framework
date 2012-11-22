<?php
class Kwc_Directories_AjaxViewTwoOnOnePage_Directory_Model extends Kwf_Model_FnF
{
    protected $_toStringField = 'name';
    protected $_columns = array('id', 'name');
    protected $_data = array();

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
