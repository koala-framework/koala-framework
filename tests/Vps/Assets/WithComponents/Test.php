<?php
/**
 * @group Assets
 * @group Assets_Component
 * @group slow
 * slow weil sie den assets cache löschen
 */
 class Vps_Assets_WithComponents_Test extends PHPUnit_Framework_TestCase
{
    public function testIt()
    {
    }

    public function testDebug()
    {
        Vps_Assets_Cache::getInstance()->clean();
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();

        $rootComponent = 'Vps_Assets_WithComponents_Root_Component';
        Vps_Component_Data_Root::setComponentClass($rootComponent);

        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->js = true;
        $config->debug->assets->css = true;
        $config->debug->assets->printcss = true;
        $loader = new Vps_Assets_Loader($config);
        $dep = $loader->getDependencies();

        $type = 'Vps_Assets_WithComponents:Test';
        $files = $dep->getAssetUrls($type, 'js', 'web', $rootComponent);
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('processing dependencies miss'));
        $expected = array(
            '/assets/web-vps/tests/Vps/Assets/OwnConfig/file2.js',
            '/assets/web-vps/tests/Vps/Assets/OwnConfig/file1.js',
            '/assets/web-vps/Ext/ext-lang-en.js'
        );
        $this->assertEquals($expected, $files);

        $c = $loader->getFileContents('web-vps/tests/Vps/Assets/OwnConfig/file2.js');
        $this->assertEquals('file2', $c['contents']);
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('load asset'));

        $loader->getFileContents('web-vps/tests/Vps/Assets/OwnConfig/file2.js');
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('load asset'));

        Vps_Component_Data_Root::setComponentClass(null);
    }

    public function testNoDebug()
    {
        Vps_Assets_Cache::getInstance()->clean();
        $loader = new Vps_Assets_Loader();
        $loader->getDependencies()->getMaxFileMTime();
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
        $rootComponent = 'Vps_Assets_WithComponents_Root_Component';
        Vps_Component_Data_Root::setComponentClass($rootComponent);

        $this->assertEquals('none', Vps_Media_Output::getEncoding());
        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->js = false;
        $config->debug->assets->css = false;
        $config->debug->assets->printcss = false;
        $loader = new Vps_Assets_Loader($config);
        $dep = $loader->getDependencies();
        $v = $dep->getMaxFileMTime();

        $type = 'Vps_Assets_OwnConfig:Test';
        $files = $dep->getAssetUrls($type, 'js', 'web', $rootComponent);
        $expected = array(
            "/assets/all/web/$rootComponent/".Zend_Registry::get('trl')->getTargetLanguage()."/Vps_Assets_OwnConfig:Test.js?v=".$v,
        );
        $this->assertEquals($expected, $files);

        $c = $loader->getFileContents("all/web/$rootComponent/".Zend_Registry::get('trl')->getTargetLanguage()."/Vps_Assets_OwnConfig:Test.js?v=".$v);
        $this->assertContains("file2\nfile1\n", $c['contents']);

        $c = $loader->getFileContents("all/web/$rootComponent/".Zend_Registry::get('trl')->getTargetLanguage()."/Vps_Assets_OwnConfig:Test.js?v=".$v);
        $this->assertContains("file2\nfile1\n", $c['contents']);

        Vps_Component_Data_Root::setComponentClass(null);
    }
}
