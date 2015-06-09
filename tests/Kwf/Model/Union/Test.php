<?php
class Kwf_Model_Union_Test extends Kwf_Test_TestCase
{
    /**
     * @expectedException Kwf_Exception
     */
    public function testNotUniqueKey()
    {
        new Kwf_Model_Union(array(
            'columnMapping' => 'Kwc_Mail_Recipient_Mapping',
            'models' => array(
                'foo' => new Kwf_Model_FnF(),
                'foobar' => new Kwf_Model_FnF()
            )
        ));
    }
}
