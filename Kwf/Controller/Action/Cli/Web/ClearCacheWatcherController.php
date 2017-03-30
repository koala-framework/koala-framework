<?php
class Kwf_Controller_Action_Cli_Web_ClearCacheWatcherController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'watch filesystem for modification and clear affected caches';
    }

    public function indexAction()
    {
        $port = null;
        if (file_exists('temp/webpack-dev-server-port')) {
            $port = file_get_contents('temp/webpack-dev-server-port');
        }
        while (true) {
            if ($port) {
                $r = @socket_create_listen($port);
                if ($r) {
                    socket_close($r);
                    break;
                }
                echo "port $port not available, choosing a random one";
            }
            $port = rand(1024, 65535);
        }
        file_put_contents('temp/webpack-dev-server-port', $port);
        $host = trim(`hostname`);
        $cmd = "NODE_PATH=vendor/koala-framework/koala-framework/node_modules_add vendor/bin/node node_modules/.bin/webpack-dev-server --progress --host=$host --port=$port --color";
        echo $cmd."\n";
        passthru($cmd);
    }
}
