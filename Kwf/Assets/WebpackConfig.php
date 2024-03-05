<?php
class Kwf_Assets_WebpackConfig
{
    public static function getDevServerPort()
    {
        if (!Kwf_Config::getValue('debug.webpackDevServer')) {
            return null;
        }

        if ($port = Kwf_Config::getValue('debug.webpackDevServerPort')) {
            return $port;
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
                $port = rand(11700, 11800);
            }
            file_put_contents('cache/webpack-dev-server-port', $port);
        }
        return $port;
    }

    public static function getDevServerPublic()
    {
        if ($ret = Kwf_Config::getValue('debug.webpackDevServerPublic')) {
            return $ret;
        } else {
            return Kwf_Config::getValue('server.domain').':'.self::getDevServerPort();
        }
    }
    public static function getDevServerProxy()
    {
        $proxy = Kwf_Config::getValue('debug.webpackDevServerProxy');
        if (!$proxy) {
            $proxy = (Kwf_Config::getValue('server.https') ? 'https' : 'http') .  '://'.Kwf_Config::getValue('server.domain');
        }
        return $proxy;
    }

    public static function getDevServerUrl()
    {
        if (!Kwf_Config::getValue('debug.webpackDevServer')) {
            return null;
        }
        if ($url = Kwf_Config::getValue('debug.webpackDevServerUrl')) {
            return $url;
        } else {
            $protocol = Kwf_Config::getValue('server.https') ? 'https://' : 'http://';
            return $protocol.self::getDevServerPublic().'/';
        }
    }
}

