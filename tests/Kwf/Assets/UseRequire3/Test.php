<?php
class Kwf_Assets_UseRequire3_Test extends Kwf_Test_TestCase
{
    private $_list;
    public function setUp()
    {
        parent::setUp();
        $this->_list = new Kwf_Assets_UseRequire3_TestProviderList();
    }

    public function testDependencies()
    {
        $d = $this->_list->findDependency('A');
        $deps = $d->getRecursiveDependencies();
        $this->assertEquals(3, count($deps));
    }

    public function testDependenciesOrder()
    {
        $d = $this->_list->findDependency('A');
        $deps = $d->getFilteredUniqueDependencies('text/javascript');
        $this->assertEquals(3, count($deps));
        $this->assertEquals('B', $deps[0]->getContentsPacked()->getFileContents());
        $this->assertEquals('A', $deps[1]->getContentsPacked()->getFileContents());
        $this->assertEquals('C', $deps[2]->getContentsPacked()->getFileContents());
    }

    public function testPackageContents()
    {
        $package = new Kwf_Assets_Package($this->_list, 'A');
        $c = $package->getPackageContents('text/javascript', 'en', 0, false)->getFileContents();
        $this->assertEquals("B\nA\nC", $c);
    }
}
