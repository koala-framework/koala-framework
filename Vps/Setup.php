<?php
class Vps_Setup
{
    public static function createConfig()
    {
        if (preg_match('#/www/usr/([0-9a-z]+)/#', $_SERVER['SCRIPT_FILENAME'], $m)) {
            $user = $m[1];
        } else if (substr(__FILE__, 0, strlen('/www/public/')) == '/www/public/') {
            $user = 'vivid';
        } else {
            $user = 'production';
        }
        $webConfig = new Zend_Config_Ini('application/config.ini', $user);

        $vpsConfig = new Zend_Config_Ini(VPS_PATH.'/config.ini', $user,
                        array('allowModifications'=>true));
        $vpsConfig->merge($webConfig);

        return $vpsConfig;
    }

}