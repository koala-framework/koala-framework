<?php
class Kwf_Assets_Dependency_ProviderTest extends Kwf_Test_TestCase
{
    private $_list;
    public function setUp()
    {
        parent::setUp();
        $this->_list = new Kwf_Assets_Dependency_TestProviderList();
    }

    public function testIni()
    {
        $d = $this->_list->findDependency('Test');
        $this->assertTrue($d instanceof Kwf_Assets_Dependency_Dependencies);
        $this->assertEquals(3, count($d->getDependencies()));

        $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_RecursiveIterator($d));
        $array = iterator_to_array($it, false);
        $this->assertEquals(4, count($array));
    }

    public function testIniInvalid()
    {
        $d = $this->_list->findDependency('Invalid');
        $this->assertEquals(null, $d);
    }
}
