<?php
class Vps_Setup
{
    public static function createConfig()
    {
        if (preg_match('#/www/usr/([0-9a-z]+)/#', $_SERVER['SCRIPT_FILENAME'], $m)) {
            $section = $m[1];
        } else if (substr(__FILE__, 0, strlen('/www/public/')) == '/www/public/') {
            $section = 'vivid';
        } else if (isset($_SERVER['HTTP_HOST']) && substr($_SERVER['HTTP_HOST'], 0, 4)=='dev.') {
            $section = 'dev';
        } else {
            $section = 'production';
        }
        $webConfig = new Zend_Config_Ini('application/config.ini', $section);

        $vpsConfig = new Zend_Config_Ini(VPS_PATH.'/config.ini', $section,
                        array('allowModifications'=>true));
        $vpsConfig->merge($webConfig);

        return $vpsConfig;
    }

}
