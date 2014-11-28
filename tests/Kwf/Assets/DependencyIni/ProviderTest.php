<?php
class Kwf_Assets_DependencyIni_ProviderTest extends Kwf_Test_TestCase
{
    private $_list;
    public function setUp()
    {
        parent::setUp();
        $this->_list = new Kwf_Assets_DependencyIni_TestProviderList();
    }

    public function testOnlyFilesIn1()
    {
        $this->_assertDependencyFiles('OnlyFilesIn1', array('bar.js', 'foo.js'));
    }

    public function testOnlyDepIn1()
    {
        $this->_assertDependencyFiles('OnlyDepIn1', array('bar.js', 'foo.js'));
    }

    public function testOverrideDepIn1()
    {
        $this->_assertDependencyFiles('OverrideDepIn1', array('bar2.js', 'foo.js'));
    }

    public function testOverrideFilesIn1()
    {
        $this->_assertDependencyFiles('OverrideFilesIn1', array('bar.js', 'foo2.js'));
    }

    public function testOnlyDep()
    {
        $this->_assertDependencyFiles('OnlyDep', array('bar.js'));
    }

    private function _assertDependencyFiles($dependencyName, $expectedFileNames)
    {
        $d = $this->_list->findDependency($dependencyName);
        $this->assertNotNull($d);
        $this->assertTrue($d instanceof Kwf_Assets_Dependency_Dependencies);

        $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_Iterator_Recursive($d, Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL));
        $fileNames = array();
        foreach ($it as $i) {
            $fileNames[] = substr($i, strrpos($i, '/')+1);
        }
        $this->assertEquals($fileNames, $expectedFileNames);
    }
}
