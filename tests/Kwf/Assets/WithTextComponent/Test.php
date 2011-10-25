<?php
/**
 * @group Assets
 * @group Assets_Component
 * @group slow
 * slow weil sie den assets cache löschen
 */
 class Kwf_Assets_WithTextComponent_Test extends Kwf_Test_TestCase
{
    public function testDebug()
    {
        $rootComponent = 'Kwf_Assets_WithTextComponent_Root_Component';
        Kwf_Component_Data_Root::setComponentClass($rootComponent);

        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->js = true;
        $config->debug->assets->css = true;
        $config->debug->assets->printcss = true;
        $loader = new Kwf_Assets_Loader($config);
        $dep = $loader->getDependencies();

        $type = 'Kwf_Assets_WithTextComponent:Test';
        $files = $dep->getAssetUrls($type, 'js', 'web', $rootComponent);
        $this->assertContains('/assets/web-kwf/Kwf_js/Form/HtmlEditor.js', $files);

        Kwf_Component_Data_Root::setComponentClass(null);
    }
}
