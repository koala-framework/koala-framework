<?php
class Vps_Controller_Action_Cli_HelpController extends Vps_Controller_Action_Cli_Abstract
{

    public static function getHelp()
    {
        return "show help (use 'vps help <controller>' to get help for a specific controller)";
    }

    public function __call($methodName, $args)
    {
        $controllerName = substr($methodName, 0, -6);
        $commands = $this->_getCommands($controllerName);
        if (!$commands) {
            echo "Couldn't find controller $controllerName";
        } else {
            $this->_outputCommands($commands, true);
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction()
    {
        echo "VPS CLI\n\n";
        echo "avaliable commands:\n";

        $commands = $this->_getCommands();
        $this->_outputCommands($commands, $this->_getParam('help'));
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function _getCommands($controllerName = null)
    {
        $commands = $this->_processModule('cli');
        foreach ($this->_processModule('vps_controller_action_cli', $controllerName) as $cmd=>$class) {
            if (!isset($commands[$cmd])) {
                $commands[$cmd] = $class;
            }
        }
        foreach ($this->_processModule('vps_e2_controller_cli', $controllerName) as $cmd=>$class) {
            if (!isset($commands[$cmd])) {
                $commands[$cmd] = $class;
            }
        }
        if (Vps_Registry::get('config')->application->id != 'vps') {
            foreach ($this->_processModule('vps_controller_action_cli_web', $controllerName) as $cmd=>$class) {
                if (!isset($commands[$cmd])) {
                    $commands[$cmd] = $class;
                }
            }
        }
        if (file_exists(getcwd() . '/.svn')) {
            foreach ($this->_processModule('vps_controller_action_cli_svn', $controllerName) as $cmd=>$class) {
                if (!isset($commands[$cmd])) {
                    $commands[$cmd] = $class;
                }
            }
        }
        return $commands;
    }

    private function _outputCommands($commands, $showHelp)
    {
        $maxLen = 0;
        foreach ($commands as $cmd=>$class) {
            if (strlen($cmd) > $maxLen) $maxLen = strlen($cmd);
        }
        $maxLen++;
        foreach ($commands as $cmd=>$class) {
            if ($cmd == 'index') continue;
            $help = false;
            if (method_exists($class, 'getHelp')) {
                $help = call_user_func(array($class, 'getHelp'));
                if (!$help) continue;
            }
            if (count($commands) > 1) {
                echo "$cmd".str_repeat(' ', $maxLen-strlen($cmd)) . ' ';
            }
            echo "$help\n";
            if ($showHelp && method_exists($class, 'getHelpOptions')) {
                $options = call_user_func(array($class, 'getHelpOptions'));
                foreach ($options as $o) {
                    echo str_repeat(' ', $maxLen);
                    echo " --$o[param]";
                    if (isset($o['value'])) {
                        $opt = isset($o['valueOptional']) && $o['valueOptional'];
                        if ($opt) echo "[";
                        if (is_array($o['value'])) {
                            $o['value'] = implode('|', $o['value']);
                        }
                        echo "=$o[value]";
                        if ($opt) echo "]";
                    }
                    if (isset($o['help'])) {
                        echo ": $o[help]";
                    }
                    echo "\n";
                }
            }
        }
    }

    private function _processModule($module, $controllerName = null)
    {
        $ret = array();
        $d = $this->getFrontController()->getDispatcher();
        if (!$d->isValidModule($module)) return $ret;
        if ($controllerName) {
            $controllerName = strtolower(Zend_Filter::filterStatic($controllerName, 'Word_CamelCaseToDash'));
        }
        $dir = $d->getControllerDirectory($module);
        foreach (new DirectoryIterator($dir) as $d) {
            if (!$d->isFile()) continue;
            $file = $d->getFilename();
            if (!substr($file, -(strlen('Controller.php')))=='Controller.php') continue;
            $class = $module.'_'.substr($file, 0, -4);
            $class = str_replace('_', ' ', $class);
            $class = ucwords($class);
            $class = str_replace(' ', '_', $class);
            $file = substr($file, 0, -(strlen('Controller.php')));
            $cmd = strtolower(Zend_Filter::filterStatic($file, 'Word_CamelCaseToDash'));
            if ($controllerName && $cmd != $controllerName) continue;
            $ret[$cmd] = $class;
        }
        return $ret;
    }
}
