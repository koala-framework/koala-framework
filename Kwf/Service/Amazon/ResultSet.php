<?php
class Kwf_Service_Amazon_ResultSet extends Zend_Service_Amazon_ResultSet
{
    public function current()
    {
        return new Kwf_Service_Amazon_Item($this->_results->item($this->_currentIndex));
    }
}
