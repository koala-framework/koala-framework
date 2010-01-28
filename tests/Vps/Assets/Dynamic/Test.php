<?php
/**
 * @group Assets
 */
class Vps_Assets_Dynamic_Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Vps_Assets_Dynamic_Asset::$file = tempnam('/tmp', 'asset');
        file_put_contents(Vps_Assets_Dynamic_Asset::$file, 'a { color: red; }');
    }

    public function tearDown()
    {
        unlink(Vps_Assets_Dynamic_Asset::$file);
    }

    public function testDynamic()
    {
        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->js = true;
        $config->debug->assets->css = true;
        $config->debug->assets->printcss = true;
        $loader = new Vps_Assets_Loader($config);
        $dep = $loader->getDependencies();

        $type = 'Vps_Assets_Dynamic:Test';
        $files = $dep->getAssetUrls($type, 'css', 'web', false);
        $this->assertEquals(1, count($files));
        $this->assertContains('/assets/dynamic/Vps_Assets_Dynamic_Asset/Vps_Assets_Dynamic:Test', $files[0]);

        $c = $loader->getFileContents('dynamic/Vps_Assets_Dynamic_Asset/Vps_Assets_Dynamic:Test');
        $this->assertEquals('a { color: red; }', $c['contents']);
    }

    public function testMTimeFiles()
    {
        Vps_Assets_Cache::getInstance()->clean();
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();

        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->js = false;
        $config->debug->assets->css = false;
        $config->debug->assets->printcss = false;
        $loader = new Vps_Assets_Loader($config);
        $dep = $loader->getDependencies();

        $type = 'Vps_Assets_Dynamic:Test';
        $files = $dep->getAssetUrls($type, 'css', 'web', false);
        $this->assertEquals(1, count($files));
        $f = 'all/web/de/Vps_Assets_Dynamic:Test.css';
        $this->assertContains('/assets/'.$f, $files[0]);

        //erstes mal laden
        $c = $loader->getFileContents($f);
        $this->assertEquals('a { color: red; }', trim($c['contents']));
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('processing dependencies miss'));
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('load asset all'));

        //nochmal laden, wird gecached
        $c = $loader->getFileContents($f);
        $this->assertEquals('a { color: red; }', trim($c['contents']));
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('processing dependencies miss'));
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('load asset all'));

        //datei ändern, muss neu geladen werden (wegen mtimeFiles in Vps_Assets_Dynamic_Asset)
        $t = time();
        sleep(1); //damit mtime sicher größer
        file_put_contents(Vps_Assets_Dynamic_Asset::$file, 'a { color: blue; }');
        clearstatcache();
        $this->assertTrue(filemtime(Vps_Assets_Dynamic_Asset::$file) > $t);
        $c = $loader->getFileContents($f);
        $this->assertEquals('a { color: blue; }', trim($c['contents']));
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('processing dependencies miss'));
        $this->assertEquals(2, Vps_Benchmark::getCounterValue('load asset all'));
    }
/*
    public function testNoDebug()
    {
        Vps_Assets_Cache::getInstance()->clean();
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();
        $this->_testNoDebug();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('processing dependencies miss'));
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('load asset all'));
        $this->assertEquals(3, Vps_Benchmark::getCounterValue('load asset'));
        $this->_testNoDebug();
        $this->_testNoDebug();
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('processing dependencies miss'));
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('load asset all'));
        $this->assertEquals(3, Vps_Benchmark::getCounterValue('load asset'));
    }
    private function _testNoDebug()
    {
        $this->assertEquals('none', Vps_Media_Output::getEncoding());
        $config = clone Zend_Registry::get('config');
        $config->application->version = '1.0';
        $config->debug->menu = false;
        $config->debug->assets->js = false;
        $config->debug->assets->css = false;
        $config->debug->assets->printcss = false;
        $dep = new Vps_Assets_Dependencies($config);

        $type = 'Vps_Assets_OwnConfig:Test';
        $files = $dep->getAssetUrls($type, 'js', 'web', false);
        $expected = array(
            '/assets/all/web/'.Zend_Registry::get('trl')->getTargetLanguage().'/Vps_Assets_OwnConfig:Test.js?v=1.0',
        );
        $this->assertEquals($expected, $files);

        $loader = new Vps_Assets_Loader($config);

        $c = $loader->getFileContents('all/web/'.Zend_Registry::get('trl')->getTargetLanguage().'/Vps_Assets_OwnConfig:Test.js?v=1.0');
        $this->assertContains("file2\nfile1\n", $c['contents']);

        $c = $loader->getFileContents('all/web/'.Zend_Registry::get('trl')->getTargetLanguage().'/Vps_Assets_OwnConfig:Test.js?v=1.0');
        $this->assertContains("file2\nfile1\n", $c['contents']);
    }
*/
}
