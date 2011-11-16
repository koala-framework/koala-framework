<?php
class Kwf_Util_Maintenance
{
    public static function writeMaintenanceBootstrap()
    {
        if (Zend_Registry::get('config')->whileUpdatingShowMaintenancePage) {
            $offlineBootstrap  = "<?php\n";
            $offlineBootstrap .= "if (isset(\$_SERVER['REDIRECT_URL']) && substr(\$_SERVER['REDIRECT_URL'], 0, 14) == '/kwf/util/apc/') {\n";
            $offlineBootstrap .= "    require('bootstrap.php.backup');\n";
            $offlineBootstrap .= "} else {\n";
            $offlineBootstrap .= "    header(\"HTTP/1.0 503 Service Unavailable\");\n";
            $offlineBootstrap .= "    header(\"Content-Type: text/html; charset=utf-8\");\n";
            $view = new Kwf_View();
            $html = $view->render('maintenance.tpl');
            $html = str_replace("\\", "\\\\", $html);
            $html = str_replace("\"", "\\\"", $html);
            $offlineBootstrap .= "    echo \"".$html."\";\n";
            $offlineBootstrap .= "}\n";
            if (!file_exists('bootstrap.php.backup')) {
                rename('bootstrap.php', 'bootstrap.php.backup');
                file_put_contents('bootstrap.php', $offlineBootstrap);
                echo "\nwrote offline bootstrap.php\n\n";
                Kwf_Util_Apc::callClearCacheByCli(array('files' => getcwd().'/bootstrap.php'));
            }
        }
    }

    public function restoreMaintenanceBootstrap()
    {
        if (Zend_Registry::get('config')->whileUpdatingShowMaintenancePage) {
            if (file_exists('bootstrap.php.backup')) {
                rename('bootstrap.php.backup', 'bootstrap.php');
                echo "\nrestored bootstrap.php\n";
            }
            Kwf_Util_Apc::callClearCacheByCli(array('files' => getcwd().'/bootstrap.php'));
        }
    }
}
