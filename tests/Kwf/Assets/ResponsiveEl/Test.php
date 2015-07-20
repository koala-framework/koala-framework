<?php
class Kwf_Assets_ResponsiveEl_Test extends Kwf_Test_TestCase
{
    private $_list;
    public function setUp()
    {
        parent::setUp();
        $this->_list = new Kwf_Assets_ResponsiveEl_TestProviderList();
    }

    public function testDep()
    {
        $d = $this->_list->findDependency('Foo');
        $deps = $d->getRecursiveDependencies();
        $ok = false;
        foreach ($deps as $i) {
            if ($i instanceof Kwf_Assets_ResponsiveEl_JsDependency) {
                $ok = true;
            }
        }
        $this->assertTrue($ok);

    }
    public function testPackage()
    {
        $package = new Kwf_Assets_Package($this->_list, 'Foo');
        $c = $package->getPackageContents('text/javascript', 'en', 0, false)->getFileContents();
        $this->assertContains("ResponsiveEl('.test123', [350,400])", $c);
        $this->assertContains("ResponsiveEl('.test456', [350])", $c);
    }

}
