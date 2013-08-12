<?php
class Kwf_Assets_Dependency_PackageTest extends Kwf_Test_TestCase
{
    private $_list;
    public function setUp()
    {
        parent::setUp();
        $this->_list = new Kwf_Assets_Dependency_TestProviderList();
    }

    public function testPackageFiles()
    {
        $package = new Kwf_Assets_Dependency_Package($this->_list, 'Test');

        $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_RecursiveIterator($package));
        $array = iterator_to_array($it, false);
        $this->assertEquals(4, count($array));
    }

    public function testPackageInvalid()
    {
        $this->setExpectedException('Kwf_Exception');
        new Kwf_Assets_Dependency_Package($this->_list, 'Invalid');
    }

    public function testPackageContents()
    {
        $package = new Kwf_Assets_Dependency_Package($this->_list, 'Test');

        $contents = $package->getPackageContents('text/javascript; charset=utf-8', 'en');
        $contents = trim($contents);
        $contents = str_replace("\n\n", "\n", $contents);
        $this->assertEquals("foo2;\nbar2;\nfoo;\nbar;", $contents);
    }

    public function testPackageSameDepTwice()
    {
        $package = new Kwf_Assets_Dependency_Package($this->_list, 'Test3');

        $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_RecursiveIterator($package));
        $array = iterator_to_array($it, false);
        $this->assertEquals(6, count($array));

        //foo2 is dependency of Test and Test3 and thus must be returned twice (however the *same* object)
        $this->assertTrue($array[0] === $array[4]);
        $this->assertTrue($array[1] === $array[5]);
    }

    public function testPackageContentsSameDepTwice()
    {
        $package = new Kwf_Assets_Dependency_Package($this->_list, 'Test3');

        $contents = $package->getPackageContents('text/javascript; charset=utf-8', 'en');
        $contents = trim($contents);
        $contents = str_replace("\n\n", "\n", $contents);
        $this->assertEquals("foo2;\nbar2;\nfoo;\nbar;", $contents);
    }

    public function testDynamicSameDepTwice()
    {
        $package = new Kwf_Assets_Dependency_Package($this->_list, 'TestWithDynamic2');
        $urls = $package->getPackageUrls('text/javascript; charset=utf-8', 'en');
        $this->assertEquals(2, count($urls));
    }

}
