<?php
class Kwf_Model_FnF_ProxyFnFExtendedReference_Test extends Kwf_Test_TestCase
{
    public function testIt()
    {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
        $childModel = Kwf_Model_Abstract::getInstance('Kwf_Model_FnF_ProxyFnFExtendedReference_ChildModel');
        $proxyModel = Kwf_Model_Abstract::getInstance('Kwf_Model_FnF_ProxyFnFExtendedReference_ProxyModel');
        $row = $childModel->getRow(1);
        $foo = $row->getParentRow('Parent')->foo;
        $this->assertEquals(5, $foo);

        $row = $proxyModel->getRow(1);
        $foo = $row->getParentRow('Parent')->foo;
        $this->assertEquals(5, $foo);
    }
}
