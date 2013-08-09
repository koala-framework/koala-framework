<?php
class Kwf_Assets_DependencyWithComponents_Test extends Kwc_TestAbstract
{
    private $_list;
    public function setUp()
    {
        $this->_list = new Kwf_Assets_DependencyWithComponents_TestProviderList();
        parent::setUp('Kwf_Assets_DependencyWithComponents_Root');
    }

    public function testPackageFiles()
    {
        $package = new Kwf_Assets_Dependency_Package($this->_list, 'Frontend');

        $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_RecursiveIterator($package));
        $array = iterator_to_array($it, false);
        $this->assertEquals(3, count($array));
    }

    public function testPackageContents()
    {
        $package = new Kwf_Assets_Dependency_Package($this->_list, 'Frontend');

        $contents = $package->getPackageContents('text/javascript; charset=utf-8');
        $contents = trim($contents);
        $contents = str_replace("\n\n", "\n", $contents);
        $this->assertEquals("foo;\nbar;", $contents);

        $contents = $package->getPackageContents('text/css');
        $contents = trim($contents);
        $contents = str_replace("\n\n", "\n", $contents);
        $this->assertEquals("testComponentCss", $contents);
    }
}
