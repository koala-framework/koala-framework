<?php
class Kwc_Directories_MapView_Directory_Model extends Kwf_Model_FnF
{
    protected $_toStringField = 'name';
    protected $_columns = array('id', 'name', 'latitude', 'longitude');
    protected $_data = array();

    protected function _init()
    {
        parent::_init();
        $latitude = 47.95334614;
        $longitude = 13.24444771;
        for ($i=1; $i<50; $i++) {
            $latitude -= 0.05;
            $longitude -= 0.05;
            $this->_data[] = array(
                'id' => $i,
                'name' => 'foo'.$i,
                'latitude' => $latitude,
                'longitude' => $longitude
            );
        }
    }
}
