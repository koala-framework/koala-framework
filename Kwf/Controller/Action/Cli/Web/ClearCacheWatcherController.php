<?php
use Symfony\Component\Process\Process;
class Kwf_Controller_Action_Cli_Web_ClearCacheWatcherController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'watch filesystem for modification and clear affected caches';
    }

    public function indexAction()
    {
        $port = Kwf_Assets_WebpackConfig::getDevServerPort();

        $devBuild = getenv('KWF_BUILD_DEV');
        if ($devBuild == '') $devBuild = '1'; //in clear-cache-watcher enable dev build by default

        $cmd = "NODE_PATH=vendor/koala-framework/koala-framework/node_modules_build KWF_BUILD_DEV=$devBuild vendor/bin/node node_modules/.bin/webpack-dev-server --progress --host=0.0.0.0 --port=$port --color";
        if (Kwf_Assets_WebpackConfig::getDevServerPublic()) {
            $cmd .= " --public=".Kwf_Assets_WebpackConfig::getDevServerPublic();
        }
        if (Kwf_Config::getValue('server.https')) {
            $cmd .= " --https";

            $ssl = Kwf_Config::getValueArray('debug.webpackDevServerSSL');
            if ($ssl['key']) $cmd .= " --key {$ssl['key']}";
            if ($ssl['cert']) $cmd .= " --cert {$ssl['cert']}";
            if ($ssl['cacert']) $cmd .= " --cacert {$ssl['cacert']}";
        }
        echo $cmd."\n";

        $process = new Process($cmd);
        $process->setTimeout(null);
        $process->start(function ($type, $buffer) {
            if (Process::ERR === $type) {
                fwrite(STDERR, $buffer);
            } else {
                fwrite(STDOUT, $buffer);
            }
        });
        file_put_contents('temp/webpack-dev-server-pid', $process->getPid());

        $cmd = "php bootstrap.php clear-view-cache --class=Kwc_Box_Assets_Component --force";
        system($cmd);

        $ret = $process->wait();
        exit($ret);
    }
}
