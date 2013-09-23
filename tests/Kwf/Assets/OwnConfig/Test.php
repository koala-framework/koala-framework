<?php
/**
 * @group Assets
 * @group slow
 * slow weil sie den assets cache löschen
 */
class Kwf_Assets_OwnConfig_Test extends Kwf_Test_TestCase
{
    public function testDebug()
    {
        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->js = true;
        $config->debug->assets->css = true;
        $config->debug->assets->printcss = true;
        $loader = new Kwf_Assets_Loader($config);
        $dep = $loader->getDependencies();

        $type = 'Kwf_Assets_OwnConfig:Test';
        $files = $dep->getAssetUrls($type, 'js', 'web', false);
        $expected = array(
            '/assets/web-kwf/tests/Kwf/Assets/OwnConfig/file2.js',
            '/assets/web-kwf/tests/Kwf/Assets/OwnConfig/file1.js',
            '/assets/web-kwf/Ext/ext-lang-en.js'
        );
        $this->assertEquals($expected, $files);

        $c = $loader->getFileContents('web-kwf/tests/Kwf/Assets/OwnConfig/file2.js');
        $this->assertEquals('file2', $c['contents']);
    }

    public function testNoDebug()
    {
        Kwf_Assets_Cache::getInstance()->clean();
        $loader = new Kwf_Assets_Loader();
        $loader->getDependencies()->getMaxFileMTime();
        Kwf_Benchmark::enable();
        Kwf_Benchmark::reset();
        $this->_testNoDebug();
        //$this->assertEquals(1, Kwf_Benchmark::getCounterValue('processing dependencies miss'));
        //$this->assertEquals(1, Kwf_Benchmark::getCounterValue('load asset all'));
        //$this->assertEquals(3, Kwf_Benchmark::getCounterValue('load asset'));
        $this->_testNoDebug();
        $this->_testNoDebug();
        //$this->assertEquals(1, Kwf_Benchmark::getCounterValue('processing dependencies miss'));
        //$this->assertEquals(1, Kwf_Benchmark::getCounterValue('load asset all'));
        //$this->assertEquals(3, Kwf_Benchmark::getCounterValue('load asset'));
    }
    private function _testNoDebug()
    {
        $this->assertEquals('none', Kwf_Media_Output::getEncoding());
        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->js = false;
        $config->debug->assets->css = false;
        $config->debug->assets->printcss = false;
        $loader = new Kwf_Assets_Loader($config);
        $dep = $loader->getDependencies();
        $v = $dep->getMaxFileMTime();

        $type = 'Kwf_Assets_OwnConfig:Test';
        $files = $dep->getAssetUrls($type, 'js', 'web', false);
        $expected = array(
            '/assets/all/web/'.Kwf_Trl::getInstance()->getTargetLanguage().'/Kwf_Assets_OwnConfig:Test.js?v='.$v,
        );
        $this->assertEquals($expected, $files);

        $c = $loader->getFileContents('all/web/'.Kwf_Trl::getInstance()->getTargetLanguage().'/Kwf_Assets_OwnConfig:Test.js');
        $this->assertContains("file2\nfile1\n", $c['contents']);

        $c = $loader->getFileContents('all/web/'.Kwf_Trl::getInstance()->getTargetLanguage().'/Kwf_Assets_OwnConfig:Test.js');
        $this->assertContains("file2\nfile1\n", $c['contents']);
    }
}
