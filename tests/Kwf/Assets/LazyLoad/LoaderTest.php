<?php
class Kwf_Assets_LazyLoad_LoaderTest extends Kwf_Test_TestCase
{
    public function testLoaderDependency()
    {
        $l = new Kwf_Assets_LazyLoad_TestProviderList();
        $d = $l->findDependency('TestLoaderDep');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(2, count($array));
    }

    public function testPackage1()
    {
        $l = new Kwf_Assets_LazyLoad_TestProviderList();
        $p = new Kwf_Assets_Package_LazyLoad($l, 'Foo', array());
        $c = $p->getPackageContents('text/javascript', 'en', 0, false)->getFileContents();
        $this->assertContains("Foo", $c);
        $this->assertContains("Bar", $c);
    }

    public function testPackage2()
    {
        $l = new Kwf_Assets_LazyLoad_TestProviderList();
        $p = new Kwf_Assets_Package_LazyLoad($l, 'Foo', array('Bar'));
        $c = $p->getPackageContents('text/javascript', 'en', 0, false)->getFileContents();
        $this->assertContains("Foo", $c);
        $this->assertNotContains("Bar", $c);
    }
}
