<?php
function p($src, $maxDepth = 5) {
    ini_set('xdebug.var_display_max_depth', $maxDepth);
    if (function_exists('xdebug_var_dump')) {
        xdebug_var_dump($src);
    } else {
        echo "<pre>";
        var_dump($src);
        echo "</pre>";
    }
}

function d($src, $maxDepth = 5)
{
    p($src, $maxDepth);
    exit;
}


class Vps_Setup
{
    public static function setUp()
    {
        require_once 'Vps/Loader.php';
        Vps_Loader::registerAutoload();

        Zend_Registry::setClassName('Vps_Registry');

        error_reporting(E_ALL);
        date_default_timezone_set('Europe/Berlin');
        set_error_handler(array('Vps_Debug', 'handleError'), E_ALL);

        $ip = get_include_path();
        foreach (Zend_Registry::get('config')->includepath as $p) {
            $ip .= PATH_SEPARATOR . $p;
        }
        set_include_path($ip);

        Zend_Db_Table_Abstract::setDefaultAdapter(Zend_Registry::get('db'));
    }

    public static function createDb()
    {
        $dao = Zend_Registry::get('dao');
        return $dao->getDb();
    }

    public static function createDao()
    {
        return new Vps_Dao(new Zend_Config_Ini('application/config.db.ini', 'database'));
    }

    public static function createConfig()
    {
        if (preg_match('#/www/(usr|public)/([0-9a-z-]+)/#', $_SERVER['SCRIPT_FILENAME'], $m)) {
            $vpsSection = $webSection = 'vivid';

            $webConfigFull = new Zend_Config_Ini('application/config.ini', null);
            if (isset($webConfigFull->{$m[2]})) {
                $webSection = $m[2];
            }

            $vpsConfigFull = new Zend_Config_Ini(VPS_PATH.'/config.ini', null);
            if (isset($vpsConfigFull->{$m[2]})) {
                $vpsSection = $m[2];
            }
        } else if (isset($_SERVER['HTTP_HOST']) && substr($_SERVER['HTTP_HOST'], 0, 4)=='dev.') {
            $vpsSection = $webSection = 'dev';
        } else {
            $vpsSection = $webSection = 'production';
        }

        $webConfig = new Zend_Config_Ini('application/config.ini', $webSection);

        $vpsConfig = new Zend_Config_Ini(VPS_PATH.'/config.ini', $vpsSection,
                        array('allowModifications'=>true));
        $vpsConfig->merge($webConfig);

        $v = $vpsConfig->application->vps->version;
        if (preg_match('#tags/([^/]+)/config\\.ini#', $v, $m)) {
            $v = $m[1];
        } else if (preg_match('#branches/([^/]+)/config\\.ini#', $v, $m)) {
            $v = $m[1];
        } else if (preg_match('#trunk/vps/config\\.ini#', $v, $m)) {
            $v = 'trunk';
        }
        $vpsConfig->application->vps->version = $v;
        if (preg_match('/Revision: ([0-9]+)/', $vpsConfig->application->vps->revision, $m)) {
            $vpsConfig->application->vps->revision = (int)$m[1];
        }
        return $vpsConfig;
    }
}
