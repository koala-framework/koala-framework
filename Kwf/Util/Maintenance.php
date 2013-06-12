<?php
class Kwf_Util_Maintenance
{
    public static function writeMaintenanceBootstrapSelf($output = true)
    {
        if (!is_writable('.') || !is_writable('bootstrap.php')) return;

        if (file_exists('bootstrap.php.backup')) {
            throw new Kwf_Exception("maintenance bootstrap already written");
        }
        $offlineBootstrap  = "<?php\n";
        $offlineBootstrap .= "if (php_sapi_name() == 'cli' || (isset(\$_SERVER['REDIRECT_URL']) &&
            (
                substr(\$_SERVER['REDIRECT_URL'], 0, 14) == '/kwf/util/apc/' ||
                \$_SERVER['REDIRECT_URL'] == '/kwf/json-progress-status' ||
                substr(\$_SERVER['REDIRECT_URL'], 0, 8) == '/assets/'
            )
        )) {\n";
        $offlineBootstrap .= "    require('bootstrap.php.backup');\n";
        $offlineBootstrap .= "} else {\n";
        $offlineBootstrap .= "    header(\"HTTP/1.0 503 Service Unavailable\");\n";
        $offlineBootstrap .= "    header(\"Content-Type: text/html; charset=utf-8\");\n";
        if (file_exists('views/maintenance.php')) {
            //dynamic maintenance page
            $offlineBootstrap .= "    include('views/maintenance.php');\n";
        } else {
            $view = new Kwf_View();
            $html = $view->render('maintenance.tpl');
            $html = str_replace("\\", "\\\\", $html);
            $html = str_replace("\"", "\\\"", $html);
            $offlineBootstrap .= "    echo \"".$html."\";\n";
        }
        $offlineBootstrap .= "}\n";

        rename('bootstrap.php', 'bootstrap.php.backup');
        file_put_contents('bootstrap.php', $offlineBootstrap);
        if ($output) echo "\nwrote offline bootstrap.php\n\n";
        Kwf_Util_Apc::callClearCacheByCli(array('files' => getcwd().'/bootstrap.php'));
    }

    public static function writeMaintenanceBootstrap($output = true)
    {
        if (!Zend_Registry::get('config')->whileUpdatingShowMaintenancePage) return;
        if (file_exists('bootstrap.php.backup')) return;

        self::writeMaintenanceBootstrapSelf($output);
        if (Kwf_Config::getValue('server.aws')) {
            $domains = Kwf_Util_Aws_Ec2_InstanceDnsNames::getOther();
            foreach ($domains as $domain) {
                if ($output) {
                    echo "executing clear-cache write-maintenance on $domain:\n";
                }
                $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php clear-cache write-maintenance";
                $cmd = "ssh $domain ".escapeshellarg('cd '.Kwf_Config::getValue('server.dir').'; '.$cmd);
                passthru($cmd);
            }
        }
    }

    public function restoreMaintenanceBootstrapSelf($output = true)
    {
        if (!file_exists('bootstrap.php.backup')) {
            throw new Kwf_Exception("maintenance bootstrap not written");
        }

        unlink('bootstrap.php');
        rename('bootstrap.php.backup', 'bootstrap.php');
        if ($output) echo "\nrestored bootstrap.php\n";
        Kwf_Util_Apc::callClearCacheByCli(array('files' => getcwd().'/bootstrap.php'));
    }

    public function restoreMaintenanceBootstrap($output = true)
    {
        if (!Zend_Registry::get('config')->whileUpdatingShowMaintenancePage) return;
        if (!file_exists('bootstrap.php.backup')) return;

        self::restoreMaintenanceBootstrapSelf($output);
        if (Kwf_Config::getValue('server.aws')) {
            $domains = Kwf_Util_Aws_Ec2_InstanceDnsNames::getOther();
            foreach ($domains as $domain) {
                if ($output) {
                    echo "executing clear-cache restore-maintenance on $domain:\n";
                }
                $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php clear-cache restore-maintenance";
                $cmd = "ssh $domain ".escapeshellarg('cd '.Kwf_Config::getValue('server.dir').'; '.$cmd);
                passthru($cmd);
            }
        }
    }
}
