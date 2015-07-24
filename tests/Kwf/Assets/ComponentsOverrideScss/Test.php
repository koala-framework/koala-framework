<?php
class Kwf_Assets_ComponentsOverrideScss_Test extends Kwc_TestAbstract
{
    private $_list;
    public function setUp()
    {
        $this->_list = new Kwf_Assets_ComponentsOverrideScss_TestProviderList();
        parent::setUp('Kwf_Assets_ComponentsOverrideScss_Root');
    }

    public function testPackageFiles()
    {
        $package = new Kwf_Assets_Package($this->_list, 'Frontend');

        $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_Iterator_Recursive($package->getDependency(), Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL));
        $array = iterator_to_array($it, false);
        $this->assertEquals(1, count($array));
    }

    public function testFindDependency()
    {
        $this->assertNotNull($this->_list->findDependency('Frontend'));
    }

    public function testPackageContents()
    {
        $package = new Kwf_Assets_Package($this->_list, 'Frontend');

        $contents = $package->getPackageContents('text/css', 'en', 0, false)->getFileContents();
        $contents = trim($contents);
        $contents = str_replace("\n\n", "\n", $contents);
        $this->assertEquals("p{testComponent:2}", $contents);
    }
}
