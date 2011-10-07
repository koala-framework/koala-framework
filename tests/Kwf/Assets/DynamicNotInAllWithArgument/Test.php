<?php
/**
 * @group Assets
 * @group slow
 * slow weil sie den assets cache löschen
 */
class Kwf_Assets_DynamicNotInAllWithArgument_Test extends Kwf_Test_TestCase
{
    private $_loader;
    public function setUp()
    {
        parent::setUp();
        Kwf_Assets_DynamicNotInAllWithArgument_Asset::$file = tempnam('/tmp', 'asset');
        file_put_contents(Kwf_Assets_DynamicNotInAllWithArgument_Asset::$file, 'a { color: {arg}; }');
        Kwf_Assets_Cache::getInstance()->clean();
        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->css = false;
        $this->_loader = new Kwf_Assets_Loader($config);
    }

    public function tearDown()
    {
        unlink(Kwf_Assets_DynamicNotInAllWithArgument_Asset::$file);
        parent::tearDown();
    }

    public function testIt()
    {
        $type = 'Kwf_Assets_DynamicNotInAllWithArgument:Test';
        $files = $this->_loader->getDependencies()->getAssetUrls($type, 'css', 'web', false);
        $this->assertEquals(2, count($files));
        $f = 'dynamic/Kwf_Assets_DynamicNotInAllWithArgument:Test/Kwf_Assets_DynamicNotInAllWithArgument_Asset:blue';
        $this->assertContains('/assets/all/web/en/Kwf_Assets_DynamicNotInAllWithArgument:Test.css', $files[0]);
        $this->assertContains('/assets/'.$f, $files[1]);

        $c = $this->_loader->getFileContents($f);
        $this->assertEquals('a { color: blue; }', trim($c['contents']));
    }
}
