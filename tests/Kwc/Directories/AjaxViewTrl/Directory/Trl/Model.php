<?php
class Kwc_Directories_AjaxViewTrl_Directory_Trl_Model extends Kwf_Model_FnF
{
    protected $_primaryKey = 'component_id';
    protected $_columns = array('component_id', 'name', 'visible');
    protected $_data = array();

    protected function _init()
    {
        parent::_init();
        for ($i=1; $i<=50; $i++) {
            $this->_data[] = array(
                'component_id' => 'root-en_directory_'.$i,
                'name' => 'footrl'.$i,
                'visible' => true
            );
        }
    }
}
