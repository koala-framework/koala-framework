<?php
class Kwf_Model_Union_FnF_Test extends Kwf_Model_Union_Abstract_Test
{

    public function setUp()
    {
        parent::setUp();
        $this->_m = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_FnF_TestModel');
    }
}