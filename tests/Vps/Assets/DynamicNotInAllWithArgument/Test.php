<?php
/**
 * @group Assets
 * @group slow
 * slow weil sie den assets cache löschen
 */
class Vps_Assets_DynamicNotInAllWithArgument_Test extends Vps_Test_TestCase
{
    private $_loader;
    public function setUp()
    {
        parent::setUp();
        Vps_Assets_DynamicNotInAllWithArgument_Asset::$file = tempnam('/tmp', 'asset');
        file_put_contents(Vps_Assets_DynamicNotInAllWithArgument_Asset::$file, 'a { color: {arg}; }');
        Vps_Assets_Cache::getInstance()->clean();
        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->css = false;
        $this->_loader = new Vps_Assets_Loader($config);
    }

    public function tearDown()
    {
        unlink(Vps_Assets_DynamicNotInAllWithArgument_Asset::$file);
        parent::tearDown();
    }

    public function testIt()
    {
        $type = 'Vps_Assets_DynamicNotInAllWithArgument:Test';
        $files = $this->_loader->getDependencies()->getAssetUrls($type, 'css', 'web', false);
        $this->assertEquals(2, count($files));
        $f = 'dynamic/Vps_Assets_DynamicNotInAllWithArgument:Test/Vps_Assets_DynamicNotInAllWithArgument_Asset:blue';
        $this->assertContains('/assets/all/web/en/Vps_Assets_DynamicNotInAllWithArgument:Test.css', $files[0]);
        $this->assertContains('/assets/'.$f, $files[1]);

        $c = $this->_loader->getFileContents($f);
        $this->assertEquals('a { color: blue; }', trim($c['contents']));
    }
}
