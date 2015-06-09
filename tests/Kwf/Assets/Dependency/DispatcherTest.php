<?php
class Kwf_Assets_Dependency_DispatcherTest extends Kwf_Test_TestCase
{
    private $_list;
    public function setUp()
    {
        parent::setUp();
        $this->_list = new Kwf_Assets_Dependency_TestProviderList();
    }

    public function testPackageContents()
    {
        $package = new Kwf_Assets_Package($this->_list, 'Test');
        $contents = $package->getPackageContents('text/javascript', 'en', 0, false)->getFileContents();
        $contents = trim($contents);
        $contents = str_replace("\n\n", "\n", $contents);
        $this->assertEquals("foo2;\nbar2;\nfoo;\nbar;", $contents);
    }

    public function testPackageUrls()
    {
        $package = new Kwf_Assets_Package($this->_list, 'Test');
        $urls = $package->getPackageUrls('text/javascript', 'en');
        $this->assertEquals(1, count($urls));
        //I don't care about the actual url, as long was we can dispatch it (see next test)
    }

    public function testPackageDispatch()
    {
        $package = new Kwf_Assets_Package($this->_list, 'Test');
        $urls = $package->getPackageUrls('text/javascript', 'en');
        $this->assertEquals(1, count($urls));
        $url = $urls[0];

        $output = Kwf_Assets_Dispatcher::getOutputForUrl($url, Kwf_Media_Output::ENCODING_NONE);

        $contents = trim($output['contents']);
        $contents = str_replace("\n\n", "\n", $contents);
        $this->assertRegExp("/^foo2;\nbar2;\nfoo;\nbar;\n\/\/# sourceMappingURL=/", $contents);
    }
}
