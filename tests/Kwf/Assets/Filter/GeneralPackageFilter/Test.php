<?php
class Kwf_Assets_Filter_GeneralPackageFilter_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Assets_Filter_GeneralPackageFilter_Dependency::$contents = 'body { color: $red; }';
        Kwf_Assets_Filter_GeneralPackageFilter_Dependency::$mtime = time()-1;
    }

    public function testIt()
    {
        $pl = new Kwf_Assets_Filter_GeneralPackageFilter_TestProviderList();
        $p = new Kwf_Assets_Package($pl, 'Foo');
        $c = $p->getPackageContents('text/css', 'en', false);
        $this->assertEquals($c->getFileContents(), "body { color: #ff0000; }");

        Kwf_Assets_Filter_GeneralPackageFilter_Dependency::$contents = 'body { color: $blue; }';
        Kwf_Assets_Filter_GeneralPackageFilter_Dependency::$mtime = time();

        $pl = new Kwf_Assets_Filter_GeneralPackageFilter_TestProviderList();
        $p = new Kwf_Assets_Package($pl, 'Foo');
        $c = $p->getPackageContents('text/css', 'en', false);
        $this->assertEquals($c->getFileContents(), "body { color: #0000ff; }");
    }
}
