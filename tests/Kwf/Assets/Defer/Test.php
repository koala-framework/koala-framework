<?php
class Kwf_Assets_Defer_Test extends Kwf_Test_TestCase
{
    private $_list;
    private $_package;
    public function setUp()
    {
        parent::setUp();
        $this->_list = new Kwf_Assets_Defer_TestProviderList();
        $this->_package = new Kwf_Assets_Package($this->_list, 'A');
    }

    public function testPackageUrls()
    {
        $urls = $this->_package->getPackageUrls('text/javascript', 'en');
        $this->assertCount(1, $urls);
        $urls = $this->_package->getPackageUrls('text/javascript; defer', 'en');
        $this->assertCount(1, $urls);
    }

    public function testPackageUrlsNoDefer()
    {
        $p = new Kwf_Assets_Package($this->_list, 'C');
        $urls = $p->getPackageUrls('text/javascript', 'en');
        $this->assertCount(1, $urls);
        $urls = $p->getPackageUrls('text/javascript; defer', 'en');
        $this->assertCount(0, $urls);
    }

    public function testPackageContents()
    {
        $c = $this->_package->getPackageContents('text/javascript', 'en', 0, false)->getFileContents();
        $c = str_replace("\n", '', $c);
        $this->assertEquals($c, 'CA');

        $c = $this->_package->getPackageContents('text/javascript; defer', 'en', 0, false)->getFileContents();
        $c = str_replace("\n", '', $c);
        $this->assertEquals($c, 'DB');
    }

    public function testGetFilteredUniqueDependencies1()
    {
        $d = $this->_list->findDependency('A')->getFilteredUniqueDependencies('text/javascript');
        $this->assertCount(2, $d);
    }

    public function testGetFilteredUniqueDependencies2()
    {
        $d = $this->_list->findDependency('A')->getFilteredUniqueDependencies('text/javascript; defer');
        $this->assertCount(2, $d);
    }

    public function testGetFilteredUniqueDependencies3()
    {
        $d = $this->_list->findDependency('B')->getFilteredUniqueDependencies('text/javascript; defer');
        $this->assertCount(3, $d);
    }

    public function testGetFilteredUniqueDependencies4()
    {
        $d = $this->_list->findDependency('C')->getFilteredUniqueDependencies('text/javascript; defer');
        $this->assertCount(0, $d);
    }

    public function testGetFilteredUniqueDependencies5()
    {
        $d = $this->_list->findDependency('C')->getFilteredUniqueDependencies('text/javascript');
        $this->assertCount(1, $d);
    }
}
