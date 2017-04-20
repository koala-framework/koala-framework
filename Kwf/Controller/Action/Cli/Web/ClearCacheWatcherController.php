<?php
class Kwf_Controller_Action_Cli_Web_ClearCacheWatcherController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'watch filesystem for modification and clear affected caches';
    }

    public function indexAction()
    {
        if (!Kwf_Config::getValue('debug.webpackDevServer')) {
            throw new Kwf_Exception("webpackDevServer is not enabled");
        }
        $port = null;
        if (file_exists('cache/webpack-dev-server-port')) {
            $port = file_get_contents('cache/webpack-dev-server-port');
        } else {
            while (true) {
                if ($port) {
                    $r = @socket_create_listen($port);
                    if ($r) {
                        socket_close($r);
                        break;
                    }
                }
                $port = rand(1024, 65535);
            }
            file_put_contents('cache/webpack-dev-server-port', $port);
        }
        $cmd = "NODE_PATH=vendor/koala-framework/koala-framework/node_modules_build vendor/bin/node node_modules/.bin/webpack-dev-server --progress --host=0.0.0.0 --port=$port --color";
        echo $cmd."\n";
        passthru($cmd);
    }
}
