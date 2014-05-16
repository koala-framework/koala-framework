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
        $this->assertEquals(3, count($d->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL)));

        $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_Iterator_Recursive($d, Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL));
        $array = iterator_to_array($it, false);
        $this->assertEquals(4, count($array));
    }

    public function testIniInvalid()
    {
        $d = $this->_list->findDependency('Invalid');
        $this->assertEquals(null, $d);
    }

    public function testStar()
    {
        $d = $this->_list->findDependency('TestWithStar');
        $this->assertTrue($d instanceof Kwf_Assets_Dependency_Dependencies);

        $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_Iterator_Recursive($d, Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL));
        $array = iterator_to_array($it, false);

        //foo2.js must be only once in array (not added again thru *)
        $this->assertEquals(4, count($array));

        //foo2.js must be first
        $this->assertContains('foo2.js', $array[0]->getAbsoluteFileName()); //foo2 must be first
    }
}
