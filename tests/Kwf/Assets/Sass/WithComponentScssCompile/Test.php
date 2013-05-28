<?php
/**
 * @group Sass
 */
class Kwf_Assets_Sass_WithComponentScssCompile_Test extends Kwf_Test_TestCase
{
    public function testIt()
    {
        Kwf_Assets_Cache::getInstance()->clean();
        $loader = new Kwf_Assets_Loader();
        $loader->getDependencies()->getMaxFileMTime();
        Kwf_Benchmark::enable();
        Kwf_Benchmark::reset();

        $rootComponent = 'Kwf_Assets_Sass_WithComponentScssCompile_Root_Component';
        Kwf_Component_Data_Root::setComponentClass($rootComponent);

        $this->assertEquals('none', Kwf_Media_Output::getEncoding());
        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->js = false;
        $config->debug->assets->css = false;
        $config->debug->assets->printcss = false;
        $loader = new Kwf_Assets_Loader($config);
        $dep = $loader->getDependencies();
        $v = $dep->getMaxFileMTime();

        $type = 'Kwf_Assets_Sass_WithComponentScssCompile:Test';
        $files = $dep->getAssetUrls($type, 'css', 'web', $rootComponent);
        $expected = array(
            "/assets/all/web/$rootComponent/".Kwf_Trl::getInstance()->getTargetLanguage()."/Kwf_Assets_Sass_WithComponentScssCompile:Test.css?v=".$v,
        );
        $this->assertEquals($expected, $files);

        $c = $loader->getFileContents("all/web/$rootComponent/".Kwf_Trl::getInstance()->getTargetLanguage()."/Kwf_Assets_Sass_WithComponentScssCompile:Test.css");
        $this->assertContains("body { height: 50px; }\n", $c['contents']);

        $c = $loader->getFileContents("all/web/$rootComponent/".Kwf_Trl::getInstance()->getTargetLanguage()."/Kwf_Assets_Sass_WithComponentScssCompile:Test.css");
        $this->assertContains("body { height: 50px; }\n", $c['contents']);

        Kwf_Component_Data_Root::setComponentClass(null);
    }
}
