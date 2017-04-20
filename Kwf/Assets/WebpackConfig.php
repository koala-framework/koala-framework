<?php
class Kwf_Assets_WebpackConfig
{
    public static function getDevServerPort()
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
        return $port;
    }
}

