<?php
class Kwf_Controller_Action_Cli_Web_WebpackConfigController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $proxy = Kwf_Config::getValue('debug.webpackDevServerProxy');
        if (!$proxy) {
            $proxy = (Kwf_Config::getValue('server.https') ? 'https' : 'http') .  '://'.Kwf_Config::getValue('server.domain');
        }
        $out = array(
            'domain' => Kwf_Config::getValue('server.domain'),
            'webpack-dev-server-url' => Kwf_Assets_WebpackConfig::getDevServerUrl(),
            'webpack-dev-server-proxy' => $proxy
        );
        echo json_encode($out);
        exit;
    }
}

