<?php
class Kwf_Assets_DefaultDependencies_Test extends Kwf_Test_TestCase
{
    public function testIt()
    {
        $package = new Kwf_Assets_Package(new Kwf_Assets_DefaultDependencies_TestProviderList(), 'Foo');
        $contents = $package->getPackageContents('text/javascript', 'en', 0, false)->getFileContents();
        $contents = trim($contents);
        $contents = str_replace("\n\n", "\n", $contents);
        $this->assertEquals("bar;\nfoo;", $contents);

    }
}
