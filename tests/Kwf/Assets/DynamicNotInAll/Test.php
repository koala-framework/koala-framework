<?php
/**
 * @group Assets
 * @group slow
 * slow weil sie den assets cache löschen
 */
class Kwf_Assets_DynamicNotInAll_Test extends Kwf_Test_TestCase
{
    private $_loader;

    public function setUp()
    {
        parent::setUp();
        Kwf_Assets_DynamicNotInAll_Asset::$file = tempnam('/tmp', 'asset');
        file_put_contents(Kwf_Assets_DynamicNotInAll_Asset::$file, 'a { color: red; }');
        Kwf_Assets_Cache::getInstance()->clean();

        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->css = false;
        $this->_loader = new Kwf_Assets_Loader($config);
    }

    public function tearDown()
    {
        parent::tearDown();
        unlink(Kwf_Assets_DynamicNotInAll_Asset::$file);
    }

    public function testIt()
    {
        $type = 'Kwf_Assets_DynamicNotInAll:Test';
        $files = $this->_loader->getDependencies()->getAssetUrls($type, 'css', 'web', false);
        $this->assertEquals(2, count($files));
        $f1 = 'all/web/en/Kwf_Assets_DynamicNotInAll:Test.css';
        $f2 = 'dynamic/Kwf_Assets_DynamicNotInAll:Test/Kwf_Assets_DynamicNotInAll_Asset';
        $this->assertContains('/assets/'.$f1, $files[0]);
        $this->assertContains('/assets/'.$f2, $files[1]);

        $c = $this->_loader->getFileContents($f1);
        $this->assertEquals('', trim($c['contents']));

        $c = $this->_loader->getFileContents($f2);
        $this->assertEquals('a { color: red; }', trim($c['contents']));
    }
}
