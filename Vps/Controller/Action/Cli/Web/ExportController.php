<?php
class Vps_Controller_Action_Cli_Web_ExportController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "update svn online";
    }

    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'server',
                'value'=> self::_getConfigSectionsWithHost(),
                'valueOptional' => false,
                'help' => 'where to update'
            ),
            array(
                'param'=> 'with-library',
                'help' => 'updates library as well'
            ),
            array(
                'param'=> 'skip-update',
                'help' => 'skip update scripts and so don\'t clear caches'
            )
        );
    }

    public function indexAction()
    {
        $config = Vps_Config_Web::getInstance($this->_getParam('server'));
        echo "updating ".$this->_getParam('server')."....\n";
        $this->_helper->viewRenderer->setNoRender(true);

        $options = array(
            'with-library' => $this->_getParam('with-library'),
            'skip-update' => $this->_getParam('skip-update'),
            'debug' => $this->_getParam('debug'),
        );
        Vps_Util_Server::export($config, $options);

        if (isset($config->server->subSections) && $config->server->subSections) {
            foreach ($config->server->subSections as $section) {
                $config = Vps_Config_Web::getInstance($section);
                echo "\nupdating $section...\n";
                Vps_Util_Server::export($config, $options);
            }
        }

        if (isset($config->server->subWebs) && $config->server->subWebs) {
            foreach ($config->server->subWebs as $web) {
                chdir($web);
                $ret = null;
                $cmd = "php bootstrap.php export --server=".$this->_getParam('server');
                passthru($cmd, $ret);
                chdir("..");
                if ($ret != 0) {
                    exit(1);
                }
            }
        }
    }
}
