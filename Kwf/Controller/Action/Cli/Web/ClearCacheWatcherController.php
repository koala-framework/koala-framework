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
        $cmd = "NODE_PATH=vendor/koala-framework/koala-framework/node_modules_build vendor/bin/node node_modules/.bin/webpack-dev-server --progress --host=0.0.0.0 --port=$port --color";
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
        file_put_contents('cache/webpack-dev-server-pid', $process->getPid());
        $ret = $process->wait();
        exit($ret);
    }
}
