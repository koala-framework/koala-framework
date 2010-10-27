<?php
class Vps_Controller_Action_Cli_HelpController extends Vps_Controller_Action_Cli_Abstract
{

    public static function getHelp()
    {
        return "show help";
    }

    public function indexAction()
    {

        echo "VPS CLI\n\n";
        echo "avaliable commands:\n";

        $commands = $this->_processModule('cli');
        foreach ($this->_processModule('vps_controller_action_cli') as $cmd=>$class) {
            if (!isset($commands[$cmd])) {
                $commands[$cmd] = $class;
            }
        }
        $maxLen = 0;
        foreach ($commands as $cmd=>$class) {
            if (strlen($cmd) > $maxLen) $maxLen = strlen($cmd);
        }
        foreach ($commands as $cmd=>$class) {
            if ($cmd == 'index') continue;
            $help = false;
            if (method_exists($class, 'getHelp')) {
                $help = call_user_func(array($class, 'getHelp'));
                if (!$help) continue;
                $help = " $help";
            }
            echo "$cmd".str_repeat(' ', $maxLen-strlen($cmd));
            echo "$help\n";
            if (method_exists($class, 'getHelpOptions')) {
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
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function _processModule($module)
    {
        $ret = array();
        $d = $this->getFrontController()->getDispatcher();
        if (!$d->isValidModule($module)) return $ret;
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
            $ret[$cmd] = $class;
        }
        return $ret;
    }
}
