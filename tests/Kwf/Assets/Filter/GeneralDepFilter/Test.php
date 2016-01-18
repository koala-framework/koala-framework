<?php
class Kwf_Assets_Filter_GeneralDepFilter_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Assets_Filter_GeneralDepFilter_Dependency1::$contents = 'body { color: $red; }';
        Kwf_Assets_Filter_GeneralDepFilter_Dependency1::$mtime = time()-1;

        Kwf_Assets_Filter_GeneralDepFilter_Dependency2::$contents = 'p { color: blue; }';
        Kwf_Assets_Filter_GeneralDepFilter_Dependency2::$mtime = time();
    }

    public function testIt()
    {
        $pl = new Kwf_Assets_Filter_GeneralDepFilter_TestProviderList();
        $p = new Kwf_Assets_Package($pl, 'Foo1');
        $c = $p->getPackageContents('text/css', 'en', false);
        $this->assertEquals($c->getFileContents(), "p { color: blue; }\nbody { color: #ff0000; }");

        //modify contents + mtime and make sure caches are updated correctly
        Kwf_Assets_Filter_GeneralDepFilter_Dependency1::$contents = 'body { color: $blue; }';
        Kwf_Assets_Filter_GeneralDepFilter_Dependency1::$mtime = time();

        $pl = new Kwf_Assets_Filter_GeneralDepFilter_TestProviderList();
        $p = new Kwf_Assets_Package($pl, 'Foo1');
        $c = $p->getPackageContents('text/css', 'en', false);
        $this->assertEquals($c->getFileContents(), "p { color: blue; }\nbody { color: #0000ff; }");
    }
}
