<?php
class Kwf_Assets_ModuleDeps_Test extends Kwf_Test_TestCase
{
    private $_list;
    public function setUp()
    {
        parent::setUp();
        $this->_list = new Kwf_Assets_ModuleDeps_TestProviderList();
    }

    public function testProvider()
    {
        $d = $this->_list->findDependency('Kwf.Assets.ModuleDeps.Test');
        $deps = $d->getRecursiveDependencies();
        $this->assertEquals(4, count($deps));
    }

    public function testPackageContents1()
    {
        $package = new Kwf_Assets_Package($this->_list, 'Kwf.Assets.ModuleDeps.Test');
        $c = $package->getPackageContents('text/javascript', 'en', 0, false)->getFileContents();
        $this->assertContains('console.log("hello world")', $c);
        $this->assertContains('jquery.org/license', $c);
    }

    public function testPackageContents2()
    {
        $package = new Kwf_Assets_Package($this->_list, 'Kwf.Assets.ModuleDeps.A');
        $c = $package->getPackageContents('text/javascript', 'en', 0, false)->getFileContents();
        $this->assertContains('console.log("A")', $c);
        $this->assertContains('console.log("B")', $c);
        $this->assertContains('console.log("C")', $c);
    }

    public function testPackageContents3()
    {
        $package = new Kwf_Assets_Package($this->_list, 'Kwf.Assets.ModuleDeps.C');
        $c = $package->getPackageContents('text/javascript', 'en', 0, false);
        $c = $c->getFileContents();
        $this->assertContains('console.log("C")', $c);
    }
}
