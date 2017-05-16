<?php
class Kwf_Controller_Action_Cli_Web_WebpackConfigController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $out = array(
            'domain' => Kwf_Config::getValue('server.domain'),
            'webpack-dev-server-port' => Kwf_Assets_WebpackConfig::getDevServerPort()
        );
        echo json_encode($out);
        exit;
    }
}

