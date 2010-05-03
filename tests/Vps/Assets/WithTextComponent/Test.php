<?php
/**
 * @group Assets
 * @group Assets_Component
 * @group slow
 * slow weil sie den assets cache löschen
 */
 class Vps_Assets_WithTextComponent_Test extends PHPUnit_Framework_TestCase
{
    public function testDebug()
    {
        $rootComponent = 'Vps_Assets_WithTextComponent_Root_Component';
        Vps_Component_Data_Root::setComponentClass($rootComponent);

        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->js = true;
        $config->debug->assets->css = true;
        $config->debug->assets->printcss = true;
        $loader = new Vps_Assets_Loader($config);
        $dep = $loader->getDependencies();

        $type = 'Vps_Assets_WithTextComponent:Test';
        $files = $dep->getAssetUrls($type, 'js', 'web', $rootComponent);
        $this->assertContains('/assets/web-vps/Vps_js/Form/HtmlEditor.js', $files);

        Vps_Component_Data_Root::setComponentClass(null);
    }
}
