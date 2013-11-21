<?php
class Kwf_Assets_DependencyCssByJs_ProviderTest extends Kwf_Test_TestCase
{
    private $_list;
    public function setUp()
    {
        parent::setUp();
        $this->_list = new Kwf_Assets_DependencyCssByJs_TestProviderList();
    }

    public function testOnlyFilesIn1()
    {
        $expectedFileNames = array(
            'foo.css',
            'foo.js'
        );
        $d = $this->_list->findDependency('Foo');
        $this->assertNotNull($d);
        $this->assertTrue($d instanceof Kwf_Assets_Dependency_Dependencies);

        $fileNames = array();
        foreach ($d->getRecursiveFiles() as $i) {
            $fileNames[] = substr($i, strrpos($i, '/')+1);
        }
        $this->assertEquals($fileNames, $expectedFileNames);
    }
}
