<?php
/**
 * @group Sass
 */
class Kwf_Assets_Sass_OwnConfig_Test extends Kwf_Test_TestCase
{
    public function testIt()
    {
        Kwf_Assets_Cache::getInstance()->clean();
        $loader = new Kwf_Assets_Loader();
        $loader->getDependencies()->getMaxFileMTime();
        Kwf_Benchmark::enable();
        Kwf_Benchmark::reset();

        $this->assertEquals('none', Kwf_Media_Output::getEncoding());
        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->js = false;
        $config->debug->assets->css = false;
        $config->debug->assets->printcss = false;
        $loader = new Kwf_Assets_Loader($config);
        $dep = $loader->getDependencies();
        $v = $dep->getMaxFileMTime();

        $type = 'Kwf_Assets_Sass_OwnConfig:Test';
        $files = $dep->getAssetUrls($type, 'css', 'web', false);
        $expected = array(
            '/assets/all/web/'.Kwf_Trl::getInstance()->getTargetLanguage().'/Kwf_Assets_Sass_OwnConfig:Test.css?v='.$v,
        );
        $this->assertEquals($expected, $files);

        $c = $loader->getFileContents('all/web/'.Kwf_Trl::getInstance()->getTargetLanguage().'/Kwf_Assets_Sass_OwnConfig:Test.css');
        $this->assertContains("body { height: 50px; }\nbody { width: 100px; }\n", $c['contents']);

        $c = $loader->getFileContents('all/web/'.Kwf_Trl::getInstance()->getTargetLanguage().'/Kwf_Assets_Sass_OwnConfig:Test.css');
        $this->assertContains("body { height: 50px; }\nbody { width: 100px; }\n", $c['contents']);
    }
}
