<?php
class Kwf_Model_FnF_ProxyFnFExtendedReference_Test extends Kwf_Test_TestCase
{
    public function testDirect()
    {
        $childModel = Kwf_Model_Abstract::getInstance('Kwf_Model_FnF_ProxyFnFExtendedReference_ChildModel');
        $row = $childModel->getRow(1);
        $foo = $row->getParentRow('Parent')->foo;
        $this->assertEquals(5, $foo);
    }

    public function testProxy()
    {
        $proxyModel = Kwf_Model_Abstract::getInstance('Kwf_Model_FnF_ProxyFnFExtendedReference_ProxyModel');
        $row = $proxyModel->getRow(1);
        $foo = $row->getParentRow('Parent')->foo;
        $this->assertEquals(5, $foo);
    }
}
