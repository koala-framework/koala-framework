<?php
/**
 * @group Assets
 * @group slow
 * slow weil sie den assets cache löschen
 */
class Vps_Assets_LastMTime_Test extends Vps_Test_TestCase
{
    public function testMaxFileMTime()
    {
        Vps_Assets_Cache::getInstance()->clean();
        $config = clone Zend_Registry::get('config');
        $config->debug->menu = false;
        $config->debug->assets->js = true;
        $config->debug->assets->css = true;
        $config->debug->assets->printcss = true;
        foreach ($config->assets as $assetType=>$v) {
            unset($config->assets->$assetType);
        }
        $c = new Vps_Config_Ini(dirname(__FILE__).'/config.ini', 'production');
        Vps_Config_Web::mergeConfigs($config, $c);
        $loader = new Vps_Assets_Loader($config);
        $dep = $loader->getDependencies();
        $this->assertEquals(filemtime(dirname(__FILE__).'/file1.js'), $dep->getMaxFileMTime());
    }
}
