<?php
/**
 * @group Assets
 * @group slow
 * slow weil sie den assets cache löschen
 */
class Vps_Assets_DynamicNotInAll_Test extends Vps_Test_TestCase
{
    private $_loader;

    public function setUp()
    {
        parent::setUp();
        Vps_Assets_DynamicNotInAll_Asset::$file = tempnam('/tmp', 'asset');
        file_put_contents(Vps_Assets_DynamicNotInAll_Asset::$file, 'a { color: red; }');
        Vps_Assets_Cache::getInstance()->clean();

        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->css = false;
        $this->_loader = new Vps_Assets_Loader($config);
    }

    public function tearDown()
    {
        parent::tearDown();
        unlink(Vps_Assets_DynamicNotInAll_Asset::$file);
    }

    public function testIt()
    {
        $type = 'Vps_Assets_DynamicNotInAll:Test';
        $files = $this->_loader->getDependencies()->getAssetUrls($type, 'css', 'web', false);
        $this->assertEquals(2, count($files));
        $f1 = 'all/web/en/Vps_Assets_DynamicNotInAll:Test.css';
        $f2 = 'dynamic/Vps_Assets_DynamicNotInAll:Test/Vps_Assets_DynamicNotInAll_Asset';
        $this->assertContains('/assets/'.$f1, $files[0]);
        $this->assertContains('/assets/'.$f2, $files[1]);

        $c = $this->_loader->getFileContents($f1);
        $this->assertEquals('', trim($c['contents']));

        $c = $this->_loader->getFileContents($f2);
        $this->assertEquals('a { color: red; }', trim($c['contents']));
    }
}
