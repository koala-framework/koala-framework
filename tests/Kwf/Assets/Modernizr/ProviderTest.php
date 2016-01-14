<?php
class Kwf_Assets_Modernizr_ProviderTest extends Kwf_Test_TestCase
{
    private $_list;
    public function setUp()
    {
        parent::setUp();
        $this->_list = new Kwf_Assets_Modernizr_TestProviderList();
    }

    public function testSingleFeature()
    {
        $d = $this->_list->findDependency('ModernizrAnimation');
        $this->assertTrue($d instanceof Kwf_Assets_Modernizr_Dependency);
        $this->assertEquals(array('Animation'), $d->getFeatures());
    }

    public function testMultipleFeatures()
    {
        $d = $this->_list->findDependency('Test');
        $this->assertTrue($d instanceof Kwf_Assets_Dependency_Dependencies);
        $d = current($d->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL));
        $this->assertTrue($d instanceof Kwf_Assets_Modernizr_Dependency);
        $this->assertEquals(array('Animation', 'Transition'), $d->getFeatures());
    }

    public function testParseScssMixin()
    {
        $d = $this->_list->findDependency('TestParseCss');
        $deps = $d->getRecursiveDependencies();
        $ok = false;
        foreach ($deps as $i) {
            if ($i instanceof Kwf_Assets_Modernizr_Dependency) {
                $ok = true;
                $this->assertEquals(array('Csscalc'), $i->getFeatures());
            }
        }
        $this->assertTrue($ok);
    }
}
