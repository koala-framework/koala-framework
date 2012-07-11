<?php
/**
 * @group Assets
 * @group slow
 * slow weil sie den assets cache löschen
 */
class Kwf_Assets_Dynamic_Test extends Kwf_Test_TestCase
{
    private $_checkComponentModificationOriginal;

    public function setUp()
    {
        parent::setUp();
        Kwf_Assets_Dynamic_Asset::$file = tempnam('/tmp', 'asset');
        file_put_contents(Kwf_Assets_Dynamic_Asset::$file, 'a { color: red; }');

        $this->_checkComponentModificationOriginal = Kwf_Registry::get('config')->debug->componentCache->checkComponentModification;
        Kwf_Registry::get('config')->debug->componentCache->checkComponentModification = true;
        Kwf_Config::deleteValueCache('debug.componentCache.checkComponentModification');
    }

    public function tearDown()
    {
        parent::tearDown();
        unlink(Kwf_Assets_Dynamic_Asset::$file);

        Kwf_Registry::get('config')->debug->componentCache->checkComponentModification = $this->_checkComponentModificationOriginal;
        Kwf_Config::deleteValueCache('debug.componentCache.checkComponentModification');
    }

    public function testDynamic()
    {
        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->js = true;
        $config->debug->assets->css = true;
        $config->debug->assets->printcss = true;
        $loader = new Kwf_Assets_Loader($config);
        $dep = $loader->getDependencies();

        $type = 'Kwf_Assets_Dynamic:Test';
        $files = $dep->getAssetUrls($type, 'css', 'web', false);
        $this->assertEquals(1, count($files));
        $this->assertContains('/assets/dynamic/Kwf_Assets_Dynamic:Test/Kwf_Assets_Dynamic_Asset', $files[0]);

        $c = $loader->getFileContents('dynamic/Kwf_Assets_Dynamic:Test/Kwf_Assets_Dynamic_Asset');
        $this->assertEquals('a { color: red; }', $c['contents']);
    }
}
