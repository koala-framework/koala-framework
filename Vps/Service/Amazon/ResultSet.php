<?php
class Vps_Service_Amazon_ResultSet extends Zend_Service_Amazon_ResultSet
{
    public function current()
    {
        return new Vps_Service_Amazon_Item($this->_results->item($this->_currentIndex));
    }
}
