<?php
class Kwf_Assets_UseRequire2_Test extends Kwf_Test_TestCase
{
    private $_list;
    public function setUp()
    {
        parent::setUp();
        $this->_list = new Kwf_Assets_UseRequire2_TestProviderList();
    }

    public function testDependencies()
    {
        $d = $this->_list->findDependency('C');
        $deps = $d->getRecursiveDependencies();
        $this->assertEquals(4, count($deps));
    }

    public function testDependenciesOrder()
    {
        $d = $this->_list->findDependency('C');
        $deps = $d->getFilteredUniqueDependencies('text/javascript');
        $this->assertEquals(4, count($deps));
        $this->assertEquals('B', $deps[0]->getContents('en'));
        $this->assertEquals('A', $deps[1]->getContents('en'));
        $this->assertEquals('D', $deps[2]->getContents('en'));
        $this->assertEquals('C', $deps[3]->getContents('en'));
    }

    public function testPackageContents()
    {
        $package = new Kwf_Assets_Package($this->_list, 'C');
        $c = $package->getPackageContents('text/javascript', 'en', 0, false)->getFileContents();
        $this->assertEquals("B\nA\nD\nC", $c);
    }
}
